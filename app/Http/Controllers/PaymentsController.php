<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Supplier;
use Carbon\Carbon;
use App\Bank;
use App\Purchase;
use App\Payment;
use DB;
use Common;

use Exception;
use Log;

class PaymentsController extends Controller
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
            $supplier = Supplier::join('purchases','suppliers.id','purchases.supplier_id')
            ->where('purchases.purchase','=','2')
            ->orderBy("suppliers.name")
            ->distinct('suppliers.id')
            ->get(['suppliers.id','suppliers.name']);

            $bank = Bank::all();
            $payments  = Payment::with("_supplier")->get();
            $newId = Common::get_autoIncremenetVal('payments');

            $res = array(
                "suppliers" => $supplier,
                "banks" => $bank,
                "payments" => $payments,
                "newId" => $newId,

                "date_format"   => $this->rDateFormat,
                );

            if($request->ajax()){
                return Response($res, 200);
            }else{
                return View('pages.financial.payment.payment',$res);
            }

        }catch(Exception $e){
            Log::error("PaymentsController@index ".$e);
            return View("errors.exception");
        } 
    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $payment =  $request->all()["payment"];

            $payment["bill_no"] = $payment["supplierPurchase"]["bill_no"];

            $res = Payment::create($payment);

            $newId = Common::get_autoIncremenetVal('payments');

            if($request->ajax()){
                if(!is_null($res)){
                    $response = array(
                        "status" => "success",
                        "payment" => $payment,
                        "newId" => $newId,
                        "message" => trans("validation.success_store", ["attribute" => trans("glob.payment")]) 
                        );
                }else{
                    $response = array(
                        "status" => "error",
                        "payment" => null,
                        "newId" => $newId,
                        "message" => trans("validation.error_store", ["attribute" => trans("glob.payment")]) 
                        );
                }
                return Response($response, 200);
            }else{
                return $response;
            }

        }catch(Exception $e){
            Log::error("PaymentsController@store ".$e);
            return View("errors.exception");
        } 
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {

        try{

            $editPayment = Payment::find($id);

            $supplierPuechases = $this->getSupplierPurchase($editPayment["bill_no"]);

            if(isset($supplierPuechases[0])){
                $editPayment["supplierPurchase"] = $supplierPuechases[0];
            }else{
                $editPayment["supplierPurchase"] = null;
            }
            $bill = $editPayment["bill_no"];

            $paid_amount = DB::select("select ROUND( sum(py.paid_amount) , $this->amt_fix)  as paid_amount
                from purchases p
                left outer join payments py on py.bill_no = p.supplierBillNo
                where p.purchase = 2 and 
                p.supplierBillNo like '$bill'
                group by p.supplierBillNo");

            if(isset($paid_amount[0])){
                $editPayment["supplierPurchase"]->total_paid_amount = Common::fixIt( $paid_amount[0]->paid_amount);
            }else{
                $editPayment["supplierPurchase"]->total_paid_amount = Common::fixIt(0);
            }

            $response = array(
                "editPayment" => $editPayment
                );

            if($request->ajax()){
                return Response($response, 200);
            }else{
                return $response;
            }

        }catch(Exception $e){
            Log::error("PaymentsController@show ".$e);
            return View("errors.exception");
        }

    }

    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        try{
            $payment = Payment::find($id);
            $payment->fill($request->all()["payment"]);
            $payment->save();

            $response = array(
                "status" => "success",
                "payment" => $payment,
                "message" => trans("validation.success_update", ["attribute" => trans("glob.payment")]) 
                );

            if($request->ajax()){
                return Response($response, 200);
            }else{
                return $response;
            }

        }catch(Exception $e){
            Log::error("PaymentsController@update ".$e);
            return View("errors.exception");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $payment  = Payment::find($id);

            (!is_null($payment)) ?  ($r = $payment->delete()) : ($r = null);

            if($r){
                $res = array('status' =>  "success", "data" =>array(), "message" =>trans("validation.success_delete",["attribute" => trans('glob.payment') ]));
            }
            else{
                $res = array('status' =>  "error", "data" =>array(), "message" => trans("validation.error_delete",["attribute" => trans('glob.payment') ]));
            }

            return response($res, 200);

        }catch(Exception $e){
            Log::error("PaymentsController@destroy ".$e);
            return View("errors.exception");
        }
    }

    public function getBills($id, Request $request)
    {
        try{

            $supplierPurchases = DB::select("select ROUND( p.billAmount, $this->amt_fix) as actual_amount, ROUND(sum(pr.billAmount),$this->amt_fix) as return_amount,  p.supplierBillNo as bill_no ,DATE_FORMAT(p.supplier_billdate,'%m/%d/%Y') as bill_date
                from purchases p
                LEFT outer JOIN purchase_returns pr on pr.supplier_billno = p.supplierBillNo
                where p.supplier_id ='".$id
                ."' and p.purchase = 2 group by bill_no, actual_amount ,p.supplier_billdate");

            $pendingSupplierPurchase = array();

            foreach ($supplierPurchases as $sp) {
                $sp = (Array)$sp;
                if($sp["return_amount"]!=null){
                    $sp["total_payable_amount"] =  Common::fixIt( $sp["actual_amount"] - $sp["return_amount"]);
                }else{
                    $sp["total_payable_amount"] =  Common::fixIt( $sp["actual_amount"]);
                    $sp["return_amount"] =  Common::fixIt(0);
                }

                $pay = Payment::select(db::raw("sum(paid_amount) as total_paid_amount"))
                ->where("bill_no","=", $sp["bill_no"])
                ->get();

                if($pay->isEmpty() || ($sp["total_payable_amount"] > $pay->first()["total_paid_amount"])){
                    if($pay->isEmpty()){
                        $sp["total_paid_amount"] = "0";
                        $sp["credit_amount"] = Common::fixIt( $sp["total_payable_amount"]);
                    }else{
                        $sp["total_paid_amount"] = Common::fixIt( $pay->first()["total_paid_amount"]);
                        $sp["credit_amount"] = Common::fixIt( $sp["total_payable_amount"] - $pay->first()["total_paid_amount"]);
                    }
                    array_push($pendingSupplierPurchase, $sp);

                }
            }

            $response = array(
                "supplierPurchases" => $pendingSupplierPurchase,
                );
            if($request->ajax()){
                return Response($response, 200);
            }else{
                return $response;
            }
        }catch(Exception $e){
            Log::error("PaymentsController@getBills ".$e);
            return View("errors.exception");
        }
    }

    public function getSupplierPurchase($bill){

        try{
            $supplierPurchases = DB::select("select ROUND( p.billAmount, $this->amt_fix) as actual_amount, ROUND( sum(pr.billAmount),$this->amt_fix)  as return_amount, ROUND( (p.billAmount - sum(pr.billAmount)),$this->amt_fix) as total_payable_amount,  p.supplierBillNo as bill_no ,DATE_FORMAT(p.supplier_billdate,'%m/%d/%Y') as bill_date
                from purchases p
                LEFT outer JOIN purchase_returns pr on pr.supplier_billno = p.supplierBillNo
                where p.purchase = 2 and 
                p.supplierBillNo like '$bill'
                group by bill_no, actual_amount ,p.supplier_billdate");

            return $supplierPurchases; 

        }catch(Exception $e){
            Log::error("PaymentsController@getSupplierPurchase ".$e);
            return View("errors.exception");
        }       
    }

    /**
     * This will return resource values of labels for printing purpose
     * @return Array of labels
     */
    public function getLabels(){
        try{
            $labels = array();
            array_push($labels, array(
                "billId"  => trans('billing.bill_no'),
                "itemName"  => trans('billing.item_name'),   
                "qty"  => trans('billing.qty'),   
                "rate"  => trans('billing.rate'),   
                "amt"  => trans('billing.amt'),   
                "item"  => trans('glob.item'),   
                "total"  => trans('glob.total'),   
                "received"  => trans('glob.received'),   
                "savedBy"  => trans('glob.saved_by'),
                "date"  => trans('glob.date'),
                "time"  => trans('glob.time'),
                "store"  => trans('glob.store'),
                "pos"  => trans('glob.pos'),
                "receipt"  => trans('glob.receipt'),
                "opening_amt" => trans('glob.opening_amt'),
                "sale" => trans('glob.sale'),   
                "cash" => trans('billing.cash'),
                "credit_card" => trans('billing.credit_card'),
                "credit" => trans('billing.credit'),
                "sale_return"   => trans('billing.sale_return'),
                "bal" => trans('billing.bal'),
                "bill_date" => trans('billing.bill_date'),
                "s_bill_no" => trans('stock.s_bill_no'),
                "s_bill_date" => trans('stock.s_bill_date'),
                "supplier" => trans('supplier.supplier'),
                "po_no" => trans('stock.po_no'),
                "mrn_no" => trans('stock.mrn_no'),
                "receiverd_by" => trans('glob.receiverd_by'),
                "purchase"  => trans('glob.purchase'),
                "mrn_date"  => trans('stock.mrn_date'),
                "barcode" => trans('glob.barcode'),
                "perticulars" => trans('glob.perticulars'),
                "discount" => trans('glob.discount'),
                "bill" => trans('billing.bill'),
                "voucher_no" => trans('glob.voucher_no'),
                "voucher_date" => trans('glob.voucher_date'),
                "goods_return_amt" => trans('glob.goods_return_amt'),
                "payment_statement1" => trans('glob.payment_statement1'),
                "payment_statement2" => trans('glob.payment_statement2'),
                "payment_statement3" => trans('glob.payment_statement3'),
                "payment_statement4" => trans('glob.payment_statement4'),
                "net_amt" => trans('glob.net_amt'),
                "payment_voucher" => trans('glob.payment_voucher'),
                "paid_amount" => trans('glob.paid_amount'),
                "remark" => trans('glob.remark'),
                "website" => trans('glob.website'), 
                "phone" => trans('glob.phone'),
                )
            );
            return $labels;
        }catch(Exception $e){
            Log::error("PaymentsController@getLabels ".$e);
            return View("errors.exception");
        } 
    }

    public function mallDetails(){
        try{
            $address = DB::select("SELECT options.value as address FROM options where options.type = 'general' and options.key = 'register_address'");
            $mallName = DB::select("SELECT options.value as mallName FROM options where options.type = 'general' and options.key = 'mall_name'");
            $logo = DB::select("SELECT options.value as logo FROM options where options.type = 'general' and options.key = 'logo'");
            $telephone = DB::select("SELECT options.value as telephone FROM options where options.type = 'general' and options.key = 'mall_telephone'");
            $mall_website = DB::select("SELECT options.value as mall_website FROM options where options.type = 'general' and options.key = 'mall_website'");

            return $res = array(      
                "address"  => (isset($address[0]->address)) ? $address[0]->address : '',   
                "mallName"  => (isset($mallName[0]->mallName)) ? $mallName[0]->mallName : '', 
                "logo"  => (isset($logo[0]->logo)) ? $logo[0]->logo : '', 
                "telephone"  => (isset($telephone[0]->telephone)) ? $telephone[0]->telephone : '',    
                "mall_website"  => (isset($mall_website[0]->mall_website)) ? $mall_website[0]->mall_website : '',          
                );

        }catch(Exception $e){
            Log::error("PaymentsController@mallDetails ".$e);
            return View("errors.exception");
        }
    }

    public function getLabelsMallDetails(Request $request){
        try{
            $labels = $this->getLabels();
            $mallDetails = $this->mallDetails();

            return Response([
                "labels" => $labels,
                "mallDetails" => $mallDetails,
                ],200);

        }catch(Exception $e){
            Log::error("PaymentsController@getLabelsMallDetails ".$e);
            return View("errors.exception");
        }

    }
}
