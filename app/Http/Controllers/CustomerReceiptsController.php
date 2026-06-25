<?php

namespace App\Http\Controllers;


use Common;

use Illuminate\Http\Request;
use App\Customer;
use App\Billing;
use App\Bank;
use Carbon\Carbon;
use App\CustomerReceipt;

use Exception;
use Log;

class CustomerReceiptsController extends Controller
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
            ->where('billings.home_delivery','=','0')
            ->distinct('customers.id')
            ->get(['customers.id','customers.name','customers.mobile']);



            $banks = Bank::all();

            $rec = CustomerReceipt::with(['customer','bank'])->get();


            $newId = Common::get_autoIncremenetVal('customer_receipts');

            $res = array(
                "customers" => $customers,
                "banks"        => $banks,
                "date" => Carbon::now()->format("Y-m-d"),
                'receiptAll' => $rec,
                'newId'     => $newId,
                "date_format"   => $this->rDateFormat,
                );


            if($request->ajax()){
                return Response($res, 200);
            }else{
                return View("pages.financial.customer_receipt.customer_receipt",$res);
            }

        }catch(Exception $e){
            Log::error("CustomerReceiptsController@index ".$e);
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
            $receipt =  $request->all()["receipt"];
            $res = CustomerReceipt::create($receipt);
            if($request->ajax()){
                if(!is_null($res)){
                    $response = array(
                        "status" => "success",
                        "receipt" => $res,
                        "message" => trans("validation.success_store", ["attribute" => trans("glob.customer_receipt")]) 
                        );
                }else{
                    $response = array(
                        "status" => "error",
                        "receipt" => null,
                        "message" => trans("validation.error_store", ["attribute" => trans("glob.customer_receipt")]) 
                        );
                }
                return Response($response, 200);
            }else{
                return $response;
            }

        }catch(Exception $e){
            Log::error("CustomerReceiptsController@store ".$e);
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
            $editReceipt = \DB::select(\DB::raw(" SELECT *,(
                SELECT ROUND(SUM(`receipt_amt`),$this->amt_fix)
                FROM customer_receipts
                WHERE against_bill_id =(  
                SELECT `against_bill_id`
                FROM `customer_receipts`
                WHERE id = ".$id." )
                ) AS paid_amount
                FROM customer_receipts
                WHERE
                id = ".$id.""));

            $bills = $this->getBillsQuery($editReceipt[0]->customer_id);


            if(isset($editReceipt[0])){
                $response = array(
                    "editReceipt" => $editReceipt[0],
                    "status"    => 'success',
                    "bills" => $bills,
                    );
            }else{
                $response = array(
                    "editReceipt" => null,
                    "status"    => 'error',
                    );
            }

            if($request->ajax()){
                return Response($response, 200);
            }else{
                return $response;
            }

        }catch(Exception $e){
            Log::error("CustomerReceiptsController@show ".$e);
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
            $receipt = CustomerReceipt::find($id);
            $receipt->fill($request->all()["receipt"]);
            $receipt->save();

            if(!is_null($receipt)){
                $response = array(
                    "status" => "success",
                    "receipt" => $receipt,
                    "message" => trans("validation.success_update", ["attribute" => trans("glob.customer_receipt")]) 
                    );
            }else{
                $response = array(
                    "status" => "Error",
                    "receipt" => '',
                    "message" => trans("validation.error_update", ["attribute" => trans("glob.customer_receipt")]) 
                    );
            }

            if($request->ajax()){
                return Response($response, 200);
            }else{
                return $response;
            }

        }catch(Exception $e){
            Log::error("CustomerReceiptsController@update ".$e);
            return View("errors.exception");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        try{
            $receipt  = CustomerReceipt::find($id);

            (!is_null($receipt)) ?  ($r = $receipt->delete()) : ($r = null);

            if($r){
                $res = array('status' =>  "success", "data" =>array(), "message" =>trans("validation.success_delete",["attribute" => trans('glob.customer_receipt') ]));
            }
            else{
                $res = array('status' =>  "error", "data" =>array(), "message" => trans("validation.error_delete",["attribute" => trans('glob.customer_receipt') ]));
            }

            return response($res, 200);

        }catch(Exception $e){
            Log::error("CustomerReceiptsController@destroy ".$e);
            return View("errors.exception");
        }
    }

    public function getBillsPerCustomer(Request $request){
        try{

            return $this->getBillsQuery($request->all()['data']);

        }catch(Exception $e){
            Log::error("CustomerReceiptsController@getBillsPerCustomer ".$e);
            return View("errors.exception");
        }
        
    }

    public function getBillsQuery($intId){
        try{

            return  \DB::select(\DB::raw("SELECT s.id, s.customer_id,
                (CASE WHEN SUM(r.receipt_amt) IS NULL THEN 0 ELSE sum(r.receipt_amt) END
                ) AS paid_amount,
                (0 - s.balance) - ((CASE WHEN SUM(r.receipt_amt) IS NULL THEN 0 ELSE SUM(r.receipt_amt)
                END) + (CASE WHEN SUM(br.amount) IS NULL THEN 0 ELSE SUM(br.amount)END)
                ) AS balance
                FROM billings s LEFT OUTER JOIN customer_receipts r
                ON r.against_bill_id = s.id
                LEFT OUTER JOIN bill_returns br
                ON s.id = br.billing_id
                where s.payment_mode = 3 AND s.home_delivery = 0  AND s.customer_id = $intId
                GROUP BY s.customer_id, s.id
                "));

        }catch(Exception $e){
            Log::error("CustomerReceiptsController@getBillsQuery ".$e);
            return View("errors.exception");
        }
    }

    
}
