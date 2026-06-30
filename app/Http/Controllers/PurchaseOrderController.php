<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Toastr;
use Common;

use App\Supplier;
use App\Item;
use App\PurchaseOrder;
use App\PurchaseOrderItem;
use App\Category;
use App\Unit;
use Excel;
use PDF;
use Carbon\Carbon;
use App;
use App\Purchase;
use App\Audit;
use Illuminate\Support\Facades\Auth;
use DB;


class PurchaseOrderController extends Controller
{
  private $rDateFormat;

  public function __construct(){
    $this->rDateFormat = Common::rDateFormat();
  }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      try{
        $purchaseOrders = PurchaseOrder::with("_supplier")
        ->leftJoin('purchases', 'purchase_orders.id', '=', 'purchases.po_no')
        ->get(['purchase_orders.*','purchases.po_no']);

        return View('pages.stock.purchase_order.purchase_order',[
          'purchaseOrders' => $purchaseOrders,
          'date_format'   =>$this->rDateFormat,
        ]);
      }
      catch(Exception $e){
        Log::error("PurchaseOrderController@index ".$e);
        return View("errors.exception");
      }
    }

    public function ajaxindex(){   

      try{
        $suppliers = Supplier::orderBy("name")->get();

        $categories=Category::all();
       // $purchaseOrders = PurchaseOrder::all();       
        $units = Unit::all();
        $newId = Common::get_autoIncremenetVal('purchase_orders');

        return Response([
          'newId'  => $newId , 
            //'items' => [] , 
          'suppliers' => $suppliers,
            //'purchaseOrders'  => [] , 
          'categories' => $categories, 
          'units' => $units ]);
      }
      catch(Exception $e){
        Log::error("PurchaseOrderController@ajaxstore ".$e);
        return View("errors.exception");
      }
    }


    public function ajaxstore(Request $request)
    {

      try{

       $inputData = $request->all()["data"];


       $orderInsert = PurchaseOrder::create($inputData);

       for($i = 0; $i < sizeof($inputData["itemList"]); $i++){
         $pkgitem = PurchaseOrderItem::create(array(
          "purchase_order_id" => $orderInsert["id"],
          "item_id" => $inputData["itemList"][$i]["item_id"],
          "unit"     => $inputData["itemList"][$i]['units']['id'],
          "cost_price"     => $inputData["itemList"][$i]['cost_price'],
          "quantity" => $inputData["itemList"][$i]["quantity"]
        ));
       }
       $response["status"] ="success";
       $response["message"] = trans("validation.success_store",["attribute" => trans('glob.purchase_order') ]);

       return Response($response, 200);
     }
     catch(Exception $e){
      Log::error("PurchaseOrderController@ajaxstore ".$e);
      return View("errors.exception");
    }
  }





  public function ajaxedit($id){
    try{
      $suppliers = Supplier::all();

      $categories=Category::all();
      $editOrder = PurchaseOrder::with("orderItemList")->with(["orderItemList.items","orderItemList.items.categories","orderItemList.units"])->find($id);
      return Response([
       // 'items' => [] , 
        'suppliers' => $suppliers, 
        'editOrder'  => $editOrder , 
        'categories' => $categories 
      ]);   
    }
    catch(Exception $e){
      Log::error("PurchaseOrderController@ajaxedit ".$e);
      return View("errors.exception");
    }     
  }


  public function ajaxupdate(Request $request, $id){
    try{

      /** check user auth */
      if (Auth::check()){
        $userId = \Auth::id();
      }

      $inputData = $request->all()["data"];
      $orderUpdate = PurchaseOrder::find($id);
      if(!is_null($orderUpdate)){
        \DB::table('purchase_order_items')->where('purchase_order_id', '=', $inputData["id"])->delete();
        $orderUpdate->fill($request->all()["data"])->save();
        for($i = 0; $i < sizeof($inputData["itemList"]); $i++){
          $pkgitem = PurchaseOrderItem::create(array(
            "purchase_order_id" => $orderUpdate["id"],
            "item_id" => $inputData["itemList"][$i]["item_id"],
            "unit"     => $inputData["itemList"][$i]['units']['id'],
            "cost_price"     => $inputData["itemList"][$i]['cost_price'],
            "quantity" => $inputData["itemList"][$i]["quantity"]
          ));
        }

        /** save audit for the purchase order with type id 2 @date->12-07-2017*/
        $auditSave = Audit::create(array(
          "record_id" => $id,
          "type" =>  2,
          "remark" =>  $inputData['remark'],
          "user_id" => $userId,
        ));

        $response["status"] ="success";
        $response["message"] = trans("validation.success_update",[ "attribute" => trans('glob.purchase_order') ]);
        return Response($response, 200);  
      }else{
        $response["status"] ="error";
        toastr::warning('Warning', trans("validation.warning_update_deleted_item",["attribute" => trans('glob.purchase_order') ]));
        return Response($response, 200); 
      } 
    }
    catch(Exception $e){
      Log::error("PurchaseOrderController@ajaxupdate ".$e);
      return View("errors.exception");
    }
  }



  public function ajaxdelete($id)
  {   
    try{
      $isPurchase = Purchase::where("po_no","=",$id)->first(); 
      if(sizeof($isPurchase) == 0){
        $PurchaseOrder = PurchaseOrder::find($id)->delete();
        PurchaseOrderItem::where('purchase_order_id', '=', $id)->delete();
        $response["status"] ="success";
        $response["message"] =trans("validation.success_delete",["attribute" => trans('glob.purchase_order') ]);
        $response["data"] = $PurchaseOrder;
        return Response($response,200);
      }else{
        return response( $res = array('status' =>  'warning' , "data" =>array(), "message" =>' Purchase Order have purchase, Cant delete Purchase Order' ), 200);
      }
    }
    catch(Exception $e){
      Log::error("PurchaseOrderController@ajaxdelete ".$e);
      return View("errors.exception");
    }
  }

  /** export to xls purchase order list */
  public function exportToXls($id){
    try{
      $orderList = PurchaseOrder::with(['orderItemList.items','orderItemList.items.categories','_supplier','_purchase'])          
      ->find($id); 

      if(sizeof($orderList)){
       $carbon = Carbon::today();
       $timestamp = $carbon->timestamp;
       $format = $carbon->format($this->rDateFormat);
       $exportName = 'PurchaseOrder-'.$format; 

       \App\Legacy\LegacyExcel::create($exportName, function ($excel) use($orderList) {

        $excel->sheet('PurchaseOrder', function ($sheet) use($orderList) {

          $sheet->appendRow(array(trans('stock.purchase_order_id'), ':'.(isset($orderList->id)) ? $orderList->id : ''),null,true,true);
          $sheet->row($sheet->getHighestRow(), function ($row) {
            $row->setFontWeight('bold');
          });
          $sheet->appendRow(array(trans('supplier.supplier_name'), ':'.(isset($orderList->_supplier->name)) ? $orderList->_supplier->name : ''));
          $sheet->row($sheet->getHighestRow(), function ($row) {
            $row->setFontWeight('bold');
          });

          $sheet->appendRow(array(trans('glob.status'), (isset($orderList->_purchase->po_no) ? 'Completed' : 'Pending')));
          $sheet->row($sheet->getHighestRow(), function ($row) {
            $row->setFontWeight('bold');
          });
          $sheet->appendRow(array('', ''));
          $sheet->appendRow(array('', ''));

          $i = 1;
          $itemList = array();
          foreach ($orderList->orderItemList as $o) {

           array_push($itemList, array(
             "Id"  => " ".$i++,
             "Item Name" => $o->items->itemName,
             "Item Code" => " ".$o->items->barcode,
             "Reference Number" =>" ".$o->items->serial_number,
             "Quantity" => " ".\Common::fixQty($o->quantity)
           )
         );
         }

         /* setting column names for data - you can of course set it manually */
         $sheet->appendRow(array_keys($itemList[0])); /* column names */

         /* getting last row number (the one we already filled and setting it to bold */
           $sheet->row($sheet->getHighestRow(), function ($row) {
            $row->setFontWeight('bold');
          });

           /* putting users data as next rows */
           foreach ($itemList as $user) {
            $sheet->appendRow($user);
          }
          $sheet->appendRow(array('', ''));
          $sheet->appendRow(array('', ''));
          $sheet->appendRow(array('You have placed order on '.(new Carbon($orderList->purchase_order_date))->format($this->rDateFormat),''));
          $sheet->row($sheet->getHighestRow(), function ($row) {
            $row->setFontWeight('bold');
          });
        });



      })->export('xlsx');

       return back();

     }else{
      toastr::warning('Warning', trans("validation.export_error"));
      return back();
    }
  }
  catch(Exception $e){
    Log::error("PurchaseOrderController@exportToXls ".$e);
    return View("errors.exception");
  }

}

public function exportToPdf($id){
  try{

    $orderList = PurchaseOrder::with(['orderItemList.items','orderItemList.items.categories','_supplier','_purchase'])          
    ->find($id); 

    if(sizeof($orderList)){

      $pdf = PDF::loadView('pages.stock.purchase_order.purchase_order_pdf', [
        'name'=> (isset($orderList->_supplier->name)) ? $orderList->_supplier->name : '', 
        'poNumber' =>(isset($orderList->id)) ? $orderList->id: '',   
        'date' => (new Carbon($orderList->purchase_order_date))->format($this->rDateFormat),
        'itemList' => $orderList->orderItemList,
        'po_no'=> (isset($orderList->_purchase->po_no)) ? 'Completed' : 'Pending',
        'i' => '1',
        'po_id' => isset($orderList->id) ? $orderList->id : '',
      ]);  

      $pdf->setPaper('a4');
      $carbon = Carbon::today();
      $timestamp = $carbon->timestamp;
      $format = $carbon->format($this->rDateFormat);
      $exportName = 'PurchaseOrder-'.$format.'.pdf'; 
      return $pdf->download($exportName);

    }else{
      toastr::warning('Warning', trans("validation.export_error"));
      return back();
    }
  }
  catch(Exception $e){
    Log::error("PurchaseOrderController@exportToPdf ".$e);
    return View("errors.exception");
  }
}


}
