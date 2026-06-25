<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer;
use App\Billing;
use PDF;
use Excel;
use Button;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use DB;
use Common;

class CustomerLedgersController extends Controller
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
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $customers = Customer::join('billings', 'customers.id', '=', 'billings.customer_id')
            ->where('billings.payment_mode','=','3')
            ->distinct('customers.id')
            ->get(['customers.id','customers.name','customers.mobile']);

            $res = array(
                "customers" => $customers
            );

            if($request->ajax()){
                return Response($res,200);
            }else{
                return View('pages.ledger.customer_ledger.customer_ledger', $res);
            }
        }
        catch(Exception $e){
            Log::error("CustomerLedgersController@index ".$e);
            return View("errors.exception");
        }
    }

    public function getLedger($id, Request $request ){
        try{

            $ledger = $this->getLedgerQuery($id);

            $res = array(
                "status" => "success",
                "ledger" => $ledger
            );
            if($request->ajax()){
                return Response($res,200);
            }else{

                return View('pages.ledger.customer_ledger.customer_ledger', $res);
            }
        }
        catch(Exception $e){
            Log::error("CustomerLedgersController@getLedger ".$e);
            return View("errors.exception");
        }
    }


    public function getLedgerQuery($id){
        try{

            return DB::select('select * , round(@bal:=@bal+ credit - debit, '.$this->amt_fix.' ) as balance 
                from (SELECT @bal:=0) as bal1, 
                (( SELECT 1 as type, b.`id` AS bill_id, CONCAT("BILL NO: ", b.id) AS particulars, 0 AS debit, 
                ROUND( ( 0 -(( CASE WHEN b.balance IS NULL THEN 0 ELSE b.balance END ) + ( CASE WHEN SUM(br.amount) IS NULL THEN 0 ELSE SUM(br.amount) END ) )),'.$this->amt_fix.') AS credit, 
                b.updated_at AS DATE, DATE_FORMAT(b.created_at,"'.$this->rDateFormatSQL.' %h:%i %p") AS created_at 
                FROM billings AS b 
                LEFT OUTER JOIN bill_returns AS br ON b.id = br.billing_id 
                WHERE b.customer_id = '.$id.' AND b.balance != 0 AND b.payment_mode = 3 GROUP BY b.id) 
                UNION 
                (SELECT 2 as type, customer_receipts.against_bill_id as bill_id,
                    CONCAT("RECEIPT NO: ", customer_receipts.against_bill_id) as particulars, 
                    ROUND(customer_receipts.receipt_amt,'.$this->amt_fix.') as debit, 0 as credit, 
                    receipt_date as date , DATE_FORMAT(created_at,"'.$this->rDateFormatSQL.' %h:%i %p") as created_at 
                    FROM customer_receipts where customer_id = '.$id.' ) ) as ledger 
                    ORDER BY created_at');
        }
        catch(Exception $e){
            Log::error("CustomerLedgersController@getLedger ".$e);
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
         
         $remark = $inputData['remark'];
         $customer = Customer::where("id", "=", $inputData['customer'])->get();

         $ledgerC = $this->getLedgerQuery($inputData['customer']);
         
         $balanace = 0 ;
         $total = 0;
         $totalDebit = 0;
         $totalCredit = 0;
         $totalBalance = 0;

         if(sizeof($ledgerC) > 0){

            if(Input::get('pdf')){
              foreach($ledgerC as $row){
                $total++;  
                $totalDebit +=$row->debit;
                $totalCredit +=$row->credit;
                $totalBalance +=$row->balance;
            }  
            if(sizeof($ledgerC) < 1){
                $totalBalance = 0;
            }else{
               $totalBalance = $ledgerC[sizeof($ledgerC)-1]->balance;     
           }

           $res = array(
            "status" => "success",
            "ledger"    => $ledgerC,
            "remark" => $remark,
            "customer" => $customer,
            "i" => "1",
            "total" => $total,
            "totalDebit" => $totalDebit,
            "totalCredit"   => $totalCredit,
            "totalBalance" => $totalBalance
        );

           $pdf = PDF::loadView('pages.ledger.customer_ledger.customer_ledger_pdf', $res); 

           $pdf->setPaper('a4');
           $carbon = Carbon::today();
           $timestamp = $carbon->timestamp;
           $format = $carbon->format($this->rDateFormat);
           $exportName = 'Customer_Ledger-'.$format.'.pdf';     
           return $pdf->download($exportName);

       }elseif (Input::get('xls')) {

        $carbon = Carbon::today();
        $timestamp = $carbon->timestamp;
        $format = $carbon->format($this->rDateFormat);
        $exportName = 'Customer_Ledger-'.$format;     
        Excel::create($exportName, function ($excel) 
            use($ledgerC,
                $total,
                $totalDebit,
                $totalCredit,
                $totalBalance,
                $remark){

             $excel->sheet('Customer_Ledger', function ($sheet) 
                use($ledgerC,
                    $total,
                    $totalDebit,
                    $totalCredit,
                    $totalBalance,
                    $remark){

                    $ledgerArray = array();
                    $i=1;

                    /* Column title row  */
                    $sheet->appendRow(array(
                     "#" ,   
                     trans('glob.date') ,
                     trans('glob.particulars') ,
                     trans('billing.credit_amt') ,
                     trans('billing.debit_amt'),
                     trans('billing.bal') ,
                 ));

                    $sheet->row($sheet->getHighestRow(), function ($row) {
                        $row->setFontWeight('bold');
                    });


                    /* data rows */
                    foreach ($ledgerC as $l) {   
                        $total++;        
                        $totalDebit = $totalDebit + $l->debit;
                        $totalCredit = $totalCredit + $l->credit;
                        $totalBalance = $totalBalance + $l->balance;             
                        $sheet->appendRow(
                            array(
                             "#" => $i++,   
                             trans('glob.date') =>  $l->created_at,
                             trans('glob.particulars') => ($l->type ==1 ) ? "Bill No. " . \Common::fixBill($l->bill_id) : "Receipt No. " .  \Common::fixBill($l->bill_id),
                             trans('billing.credit_amt') =>  (isset($l->credit)) ? " ".(\Common::fixIt($l->credit)) : "0",
                             trans('billing.debit_amt') =>  (isset($l->debit)) ? " ".(\Common::fixIt($l->debit)) : "0",
                             trans('billing.bal') =>  (isset($l->balance)) ? " ".(\Common::fixIt($l->balance)) : "0",
                         )
                        );
                    }


                    if(sizeof($ledgerC) < 1){
                        $totalBalance = 0;
                    }else{
                        $totalBalance = $ledgerC[sizeof($ledgerC)-1]->balance;     
                    }

                    /* Total calculation row */
                    $sheet->appendRow(
                      array(
                        "#" => trans('glob.total')."=".$total,
                        trans('glob.date') => "",
                        trans('glob.particulars') => "",
                        trans('billing.credit_amt') => (isset($totalCredit)) ? " ".(\Common::fixIt($totalCredit)) : "0" ,
                        trans('billing.debit_amt') => (isset($totalDebit)) ? " ".(\Common::fixIt($totalDebit)) : "0" ,                         
                        trans('billing.bal') =>  (isset($totalBalance)) ? " ".(\Common::fixIt($totalBalance)) : "0",
                    )
                  );

                    $sheet->row($sheet->getHighestRow(), function ($row) {
                        $row->setFontWeight('bold');
                    });

                    /* remark row */
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
    Log::error("CustomerLedgersController@exportTo ".$e);
    return View("errors.exception");
}
}
}
