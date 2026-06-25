<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Supplier;
use App\purchase;
use App\PurchaseReturn;
use PDF;
use Excel;
use Button;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use DB;
use Toastr;
use Common;

class SupplierLedgerController extends Controller
{
  private $amt_fix;
  private $qty_fix;
  private $bill_pad;
  private $curr;
  private $rDateFormat;
  private $rDateFormatSQL;

  public function __construct(){

    $this->amt_fix = common::dFixIt();
    $this->qty_fix =common::dQty();
    $this->bill_pad = common::dBill();
    $this->curr = common::curr();
    $this->rDateFormat = Common::rDateFormat();
    $this->rDateFormatSQL = Common::rDateFormatSQL($this->rDateFormat);
  }
    /**
     * Display a listing of the resource.
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function index(Request $request){
      try{

        $suppliers = Supplier::join('purchases','suppliers.id','purchases.supplier_id')
        ->where('purchases.purchase','=','2')
        ->orderBy("suppliers.name")
        ->distinct('suppliers.id')
        ->get(['suppliers.id','suppliers.name']);

        $res = array(
         "suppliers" => $suppliers
       );

        if($request->ajax()){
         return Response($res,200);
       }else{
         return View('pages.ledger.supplier_ledger.supplier_ledger', $res);
       }
     }
     catch(Exception $e){
      Log::error("SupplierLedgerController@index ".$e);
      return View("errors.exception");
    }
  }

    /**
    * Select data which is displayed on report view table
    * @param  REquest $request [description]
    * @return [type]           [description]
    */
    public function getLedger($id, Request $request){
     try{
       $ledger = $this->getLedgerQuery($id);

       $res = array(
         "status" => "success",
         "ledger" => $ledger
       );
       if($request->ajax()){
         return Response($res,200);
       }else{
         return $res;
         return View('pages.ledger.supplier_ledger.supplier_ledger', $res);
       }
     }
     catch(Exception $e){
      Log::error("SupplierLedgerController@getLedger ".$e);
      return View("errors.exception");
    }
  }

  public function getLedgerQuery($id){
   try{
    return DB::select('select *, round(@bal:=@bal+credit-debit, '.$this->amt_fix.') as balance  
      from (SELECT @bal:=0) as bal1,
      ((Select DATE_FORMAT(supplier_billdate, "'.$this->rDateFormatSQL.'" ) as date,
      CONCAT("Purchase Bill NO: ", supplierBillNo) as particulars, 0 as debit,  
      ROUND(billAmount,'.$this->amt_fix.') as credit, 1 as type,created_at
      from purchases
      where purchase = 2 and supplier_id = '.$id.')
      UNION
      (SELECT DATE_FORMAT(return_date, "'.$this->rDateFormatSQL.'" ) as date,
      CONCAT("Purchase Return Bill NO: ", supplier_billno) as particulars,
      ROUND(pr.billAmount,'.$this->amt_fix.') as debit, 0 as credit, 2 as type, pr.created_at
      FROM `purchase_returns` pr inner join purchases p on p.supplierBillNo = pr.supplier_billno
      where pr.supplier_id = '.$id.' and p.purchase = 2)
      UNION
      (SELECT DATE_FORMAT(payments.payment_date, "'.$this->rDateFormatSQL.'" ) as date,
      CONCAT("Payment No: ", payments.id) as particulars,
      ROUND(payments.paid_amount,'.$this->amt_fix.') as debit, 0 as credit, 3 as type, created_at
      FROM payments
      where supplier_id = '.$id.')) as ledger order by created_at,date');
  }
  catch(Exception $e){
    Log::error("SupplierLedgerController@getLedgerQuery ".$e);
    return View("errors.exception");
  }
}

/** export to Excel/PDF Customer list */
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     */
    public function exportTo(Request $request){  
      try{

       $inputData = $request->all();  
      //$id = $inputData['Supplier']; 
       $remark = $inputData['remark'];
       $supplier = Supplier::where("id", "=", $inputData['Supplier'])->get();

       $ledger = $this->getLedgerQuery($inputData['Supplier']);

       $totalDebit = 0;
       $totalCredit = 0;
       $totalBalance = 0; 
       $total=0;

       if(sizeof($ledger) > 0){

        if(Input::get('pdf')){    			
         foreach($ledger as $row){ 
          $total++;        
          $totalDebit = $totalDebit + $row->debit;
          $totalCredit = $totalCredit + $row->credit;
          $totalBalance = $totalBalance + $row->balance;
        }	

        if(sizeof($ledger) < 1){
          $totalBalance = 0;
        }else{
          $totalBalance = $ledger[sizeof($ledger)-1]->balance;     
        }

        $res = array(           
         "ledger" => $ledger,
         "remark" => $remark,
         "supplier" => $supplier,
         "i" => "1",
         "total" => $total,
         "totalDebit" => $totalDebit,
         "totalCredit"	=> $totalCredit,
         "totalBalance" => $totalBalance
       );



        $pdf = PDF::loadView('pages.ledger.supplier_ledger.supplier_ledger_pdf', $res); 

        $pdf->setPaper('a4');
        $carbon = Carbon::today();
        $timestamp = $carbon->timestamp;
        $format = $carbon->format($this->rDateFormat);
        $exportName = 'SupplierLedger-'.$format.'.pdf';     
        return $pdf->download($exportName);

      }elseif (Input::get('xls')) {
        $carbon = Carbon::today();
        $timestamp = $carbon->timestamp;
        $format = $carbon->format($this->rDateFormat);
        $exportName = 'SupplierLedger-'.$format;     
        Excel::create($exportName, function ($excel) 
          use($ledger,
            $total,
            $totalDebit,
            $totalCredit,
            $totalBalance,
            $remark){

           $excel->sheet('SupplierLedger', function ($sheet) 
            use($ledger,
              $total,
              $totalDebit,
              $totalCredit,
              $totalBalance,
              $remark){

              $ledgerArray = array();
              $i=1;

              /* setting column names for data - you can of course set it manually column names*/
              $sheet->appendRow(array(
                "#" ,
                trans('glob.date') ,
                trans('glob.particulars') ,
                trans('billing.credit_amt') ,
                trans('billing.debit_amt') ,                         
                trans('billing.bal') ,
              )); 


              /* getting last row number (the one we already filled and setting it to bold*/
                $sheet->row($sheet->getHighestRow(), function ($row) {
                 $row->setFontWeight('bold');
               });

                foreach ($ledger as $l) {  
                  $total++;        
                  $totalDebit = $totalDebit + $l->debit;
                  $totalCredit = $totalCredit + $l->credit;
                  $totalBalance = $totalBalance + $l->balance;

                  /* putting users data as next rows*/
                  $sheet->appendRow(
                    array(
                      "#" => $i++,
                      trans('glob.date')  => $l->date,
                      trans('glob.particulars') => $l->particulars,
                      trans('billing.credit_amt') => (isset($l->credit)) ? " ".(\Common::fixIt($l->credit)) : "0" ,
                      trans('billing.debit_amt') => (isset($l->debit)) ? " ".(\Common::fixIt($l->debit)) : "0" ,                         
                      trans('billing.bal') => (isset($l->balance)) ? " ".(\Common::fixIt($l->balance)) : "0" ,
                    )
                  );
                }


                if(sizeof($ledger) < 1){
                  $totalBalance = 0;
                }else{
                  $totalBalance = $ledger[sizeof($ledger)-1]->balance;     
                }

                /* total calculation row*/
                $sheet->appendRow(
                  array(
                    "#" => trans('glob.total')."=".$total,
                    trans('glob.date')  => "",
                    trans('glob.particulars') => "",
                    trans('billing.credit_amt') =>  (isset($totalCredit)) ? " ".(\Common::fixIt($totalCredit)) : "0" ,
                    trans('billing.debit_amt') =>  (isset($totalDebit)) ? " ".(\Common::fixIt($totalDebit)) : "0" ,                         
                    trans('billing.bal') =>  (isset($totalBalance)) ? " ".(\Common::fixIt($totalBalance)) : "0" ,
                  )
                );


                
                $sheet->row($sheet->getHighestRow(), function ($row) {
                  $row->setFontWeight('bold');
                });


                /*  remark row */
                $sheet->appendRow(array(trans('glob.remark'), $remark));

                $sheet->row($sheet->getHighestRow(), function ($row) {
                  $row->setFontWeight('bold');
                });


              });

         })->export('xlsx');


        return back();

      }
    }
    else{
      toastr::warning('Warning', trans("validation.export_error"));
      return back();
    }
  }
  catch(Exception $e){
    Log::error("SupplierLedgerController@exportTo ".$e);
    return View("errors.exception");
  }
}
}
