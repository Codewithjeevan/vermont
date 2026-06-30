<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
use App\Category;
use App\Unit;
use App\Supplier;
use App\Purchase;
use App\PurchasedItem;
use App\Salesman;
use App\PurchaseOrder;
use Carbon\Carbon;
use App\Stock;
use App\Payment;
use DB;
use App\PurchaseReturn;
use App\ExpiryItems;
use App\Audit;
use Illuminate\Support\Facades\Auth;

use Common;

class PurchaseController extends Controller
{
    private $rDateFormat;
    private $intVatType;

    public function __construct(){
        $this->rDateFormat = Common::rDateFormat();
        $this->intVatType=Common::vatType();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       try{

       // $categories = Category::all();
        $units = Unit::all();
        $suppliers = Supplier::orderBy("name")->get();
        $purchases = Purchase::with("_supplier")->get();
        $salesman = Salesman::all();
        $ai = \Common::get_autoIncremenetVal("purchases");
        $boolIsItemCreate = Common::can_create('item');

        /*$purchaseOrders = PurchaseOrder::whereNotIn("id", function($q){
          $q->select('po_no')->from('purchases')->where("po_no","<>",null);
      })->get();*/

        //$labels= $this->getLabels();
        //$mallDetails = $this->mallDetails();
      $res = array(
            //"items"  => [],
            //'categories' => $categories,
        "units" => $units,
        "suppliers" => $suppliers,
        "purchases" => $purchases,
        "salesman" => $salesman,
        "mrnNo" => $ai,
           // "purchaseOrders" => $purchaseOrders,
            //"labels" => $labels,
            //"mallDetails" => $mallDetails,
        'date_format'   => $this->rDateFormat,
        'boolIsItemCreate'=>$boolIsItemCreate,
    );
      if($request->ajax()){
        return Response($res,200);
    }else{
        return View("pages.stock.purchase.purchase",$res);
    }
}
catch(Exception $e){
    Log::error("PurchaseController@index ".$e);
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
            $purchases = $request->all()["item"];
            $purchasedItems = $purchases["purchasedItems"];


            $response["focus"] = array();
            $duplicate = false;

            $billNo = Purchase::where("supplierBillNo","=",$purchases["supplierBillNo"])->where("supplier_id","=",$purchases["supplier_id"])->first(); 

            if($billNo != null){
                $response["status"] ="warning";
                $response["message"] ="bill no duplication";
                $response["focus"] =[];
                array_push($response["focus"] ,"billNo");

                $duplicate = true;
            }

            if($duplicate){
                return Response($response, 200);
            }

            $purchase = Purchase::create($purchases);


            if(!is_null($purchase)){

                foreach ($purchasedItems as $p) {
                    if($p["item"]["id"]==0){
                        $cat = Category::where("parent_category","<>", 0)
                        ->where("categorie", "like", $p["item"]["categories"]["categorie"])
                        ->get();

                        if($cat->isEmpty()){
                            $newCat = Category::insertGetId(array(
                                "categorie" => $p["item"]["categories"]["categorie"],
                                "parent_category" => 1,
                                "created_at"  => Carbon::now(),
                                "updated_at" => Carbon::now()
                            ));

                        }else{
                            $newCat = $cat->first()->id;
                        }

                        $newItem = Item::insertGetId(array(
                         'category' => $newCat,
                         'barcode' => $p["item"]["barcode"], 
                         'serial_number' =>  $p["serial_number"], 
                         'itemName' => $p["item"]["itemName"],
                         'costPrise' => $p["costPrise"],
                         'salePrise' => $p["salePrise"],
                         'vat' => $p["vat"],
                         'cp_vat' => $p["cp_vat"],
                         'unit' => $p["unit"],
                         'stock' => $p["qty"],
                     ));


                        PurchasedItem::create(array(
                            "purchase_id" => $purchase->id, 
                            "item_id" => $newItem, 
                            "qty" => $p["qty"], 
                            "unit" => $p["unit"], 
                            "costPrise"  =>$p["costPrise"], 
                            "expiryDate" => $p["expiryDate"], 
                            "cp_vat"  =>$p["cp_vat"],
                            "discount" => $p["discount"], 
                            "totalDiscount" => $p["totalDiscount"], 
                            "amount" => $p["amount"]
                        ));

                        Stock::create(array(
                            "item_id" => $newItem, 
                            "type" => 2, 
                            "type_id" => $purchase->id, 
                            "stock" => $p["qty"], 
                            "date"  =>$purchase->supplier_billdate                        
                        ));                   
                        
                    }else{

                        $item = Item::find($p["item"]["id"]);
                        $item->costPrise = $p["costPrise"];
                        $item->serial_number = $p["serial_number"];
                        $item->cp_vat = $p["cp_vat"];
                        $item->stock = $item->stock + $p["qty"];
                        $item->save();

                        PurchasedItem::create(array(
                            "purchase_id" => $purchase->id, 
                            "item_id" => $p["item"]["id"], 
                            "qty" => $p["qty"], 
                            "unit" => $p["unit"], 
                            "costPrise"  =>$p["costPrise"],
                            "cp_vat"  =>$p["cp_vat"],
                            "expiryDate" => $p["expiryDate"], 
                            "discount" => $p["discount"], 
                            "totalDiscount" => $p["totalDiscount"], 
                            "amount" => $p["amount"]
                        ));
                        Stock::create(array(
                            "item_id" => $p["item"]["id"], 
                            "type" => 2, 
                            "type_id" => $purchase->id, 
                            "stock" => $item->stock, 
                            "date"  =>$purchase->supplier_billdate                        
                        ));                    
                    }
                }

                $res = array(
                    "status"  => "success",
                    "msg"   => trans("validation.success_store",[ "attribute" => trans('glob.purchase') ])
                );
            }else{
                $res = array(
                    "status"  => "Error",
                    "msg"   => trans("validation.error_store",[ "attribute" => trans('glob.purchase') ])    
                );
            }


            if($request->ajax()){

                return Response($res,200);

            }else{

                ($res["status"] == "success") 
                ? 
                toastr::success('Success', trans("validation.success_store",[ "attribute" => trans('glob.purchase') ]))
                :
                toastr::error('error', trans("validation.error_store",[ "attribute" => trans('glob.purchase') ]));
                return View("pages.stock.purchase.purchase",$res);
            }
        }
        catch(Exception $e){
            Log::error("PurchaseController@store ".$e);
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
       // $categories = Category::all();
            $units = Unit::all();
            $suppliers = Supplier::all();
        //$purchases = Purchase::all();
            
            $ePurchase =  Purchase::with(["purchasedItems.item.categories", "purchasedItems._unit"])->find($id);
            $purchaseOrders=Purchase::with('purchaseOrder')->find($id);
            
            foreach ($ePurchase['purchasedItems'] as $items) {

                $items['costPrise'] = ROUND($items['costPrise'],2);
                $items['totalDiscount'] = ROUND($items['totalDiscount'],2);
                $items['qty'] = floatval(ROUND($items['qty'],3));
                $items['serial_number'] = $items->item->serial_number;
                
            }

            $res = array(
            //"items"  => [],
            //'categories' => $categories,
                "units" => $units,
                "suppliers" => $suppliers,
            //"purchases" => $purchases,
                "ePurchases" => $ePurchase,
                "purchaseOrders"=>$purchaseOrders
            );

            if($request->ajax()){
                return Response($res,200);
            }else{
                return View("pages.stock.purchase.purchase",$res);
            }
        }
        catch(Exception $e){
            Log::error("PurchaseController@show ".$e);
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
        /** check user auth */
        if (Auth::check()){
            $userId = \Auth::id();
        }else{
            $userId=0;
        }

        $purchases = $request->all()["item"];


        $response["focus"] = array();
        $duplicate = false;

        $billNo = Purchase::where("supplierBillNo","=",$purchases["supplierBillNo"])->where("supplier_id","=",$purchases["supplier_id"])->first(); 

        if($billNo != null  && $billNo["id"] != $id){

            $response["status"] ="warning";
            $response["message"] ="bill no duplication";
            $response["focus"] =[];
            array_push($response["focus"] ,"billNo");

            $duplicate = true;


        }

        if($duplicate){
            return Response($response, 200);
        }



        $purchase = Purchase::find($id);     

        $existing = PurchasedItem::where("purchase_id","=", $id)->get();      

        if(!is_null($purchase)){
            $purchase->fill($purchases)->save();
        }

        $purchasedItems = $purchases["purchasedItems"];



        if(!is_null($purchase)){
            $this->deleteItem($existing,$purchasedItems,$id);                 

            foreach ($purchasedItems as $p) {



                if($p["item"]["id"]==0){
                    $cat = Category::where("parent_category","<>", 0)
                    ->where("categorie", "like", $p["item"]["categories"]["categorie"])
                    ->get();

                    if($cat->isEmpty()){
                        $newCat = Category::insertGetId(array(
                            "categorie" => $p["item"]["categories"]["categorie"],
                            "parent_category" => 1,
                            "created_at"  => Carbon::now(),
                            "updated_at" => Carbon::now()
                        ));
                    }else{
                        $newCat = $cat->first()->id;
                    }

                    $newItem = Item::insertGetId(array(
                     'category' => $newCat,
                     'barcode' => $p["item"]["barcode"], 
                     'serial_number' => $p["item"]["serial_number"], 
                     'itemName' => $p["item"]["itemName"],
                     'costPrise' => $p["costPrise"],
                     'salePrise' => $p["salePrise"],
                     'vat' => $p["vat"],
                     'cp_vat' => $p["cp_vat"],
                     'unit' => $p["unit"],
                     'stock' => $p["qty"],
                 ));                    

                    PurchasedItem::create(array(
                        "purchase_id" => $purchase->id, 
                        "item_id" => $newItem, 
                        "qty" => $p["qty"], 
                        "unit" => $p["unit"], 
                        "costPrise"  =>$p["costPrise"], 
                        'cp_vat' => $p["cp_vat"],
                        "expiryDate" => $p["expiryDate"], 
                        "discount" => $p["discount"], 
                        "totalDiscount" => $p["totalDiscount"], 
                        "amount" => $p["amount"]
                    ));

                    Stock::create(array(
                        "item_id" => $newItem, 
                        "type" => 2, 
                        "type_id" => $purchase->id, 
                        "stock" => $p["qty"], 
                        "date"  =>$purchase->supplier_billdate                        
                    ));               


                }else{
                    $purchasedItem_ = PurchasedItem::where("purchase_id","=",$purchase->id)
                    ->where("item_id","=", $p["item"]["id"]);

                    $purchasedItem = $purchasedItem_->get();

                    if(!is_null($purchasedItems)){
                        $prevQty = $purchasedItem->first()["qty"];
                        $purchasedItem_->delete();
                    }
                    else{
                        $prevQty = 0;
                    }


                    $item = Item::find($p["item"]["id"]);
                    $item->costPrise = $p["costPrise"];
                    $item->serial_number = $p["serial_number"];
                    $item->cp_vat = $p["cp_vat"];
                    $diff = $p["qty"] - $prevQty;
                    $item->stock = $item->stock + $diff;
                    $item->save();

                    PurchasedItem::create(array(
                        "purchase_id" => $purchase->id, 
                        "item_id" => $p["item"]["id"], 
                        "qty" => $p["qty"], 
                        "unit" => $p["unit"], 
                        "costPrise"  =>$p["costPrise"],
                        "cp_vat"  =>$p["cp_vat"],
                        "expiryDate" => $p["expiryDate"], 
                        "discount" => $p["discount"], 
                        "totalDiscount" => $p["totalDiscount"], 
                        "amount" => $p["amount"]
                    ));

                    if($prevQty != $p["qty"]){

                        $stock_ = Stock::where("type_id","=",$purchase->id)
                        ->where("item_id","=", $p["item"]["id"]);
                        $stockDetails = $stock_->get();


                        
                        if(!$stockDetails->isEmpty()){ 
                            $intActualStock = $stockDetails[0]->stock + $diff;                          
                            $stock_->update(['stock' => $intActualStock]); 
                        }else{

                         Stock::create(array(
                            "item_id" => $p["item"]["id"], 
                            "type" => 2, 
                            "type_id" => $purchase->id, 
                            "stock" => $item->stock, 
                            "date"  =>$purchase->supplier_billdate                        
                        ));
                     }
                 }

             }
         }
     }

     /** save audit for the purchase with type id 3 @date->12-07-2017*/
     $auditSave = Audit::create(array(
        "record_id" => $id,
        "type" =>  3,
        "remark" =>  $purchases['remark'],
        "user_id" => $userId,
    ));




     $res = array(
        "status"  => "success",
        "msg"   => trans("validation.success_update",[ "attribute" => trans('glob.purchase') ])
    );
     if($request->ajax()){
        return Response($res,200);
    }else{
        toastr::error('error', trans("validation.error_update",[ "attribute" => trans('glob.purchase') ]));
        return View("pages.stock.purchase.purchase",$res);
    }
}
catch(Exception $e){
    Log::error("PurchaseController@update ".$e);
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

            $purchase  = Purchase::find($id);
            if(!is_null($purchase)){
                $isPurchase  = Purchase::where("purchase", "=", 2)->where("id", "=",$id)->get();
                $isPurchaseReturn = PurchaseReturn::where("supplier_id", "=", $purchase->supplier_id)->where("supplier_billno", "=", $purchase->supplierBillNo)->get();
                $hasPaymentDone = Payment::where("supplier_id", "=", $purchase->supplier_id)->where("bill_no", "=", $purchase->supplierBillNo)->get();
                $hasExpiryStock=ExpiryItems::where("mrn_no","=",$id)->get();
                
                if(sizeof($hasPaymentDone) == 0 && sizeof($isPurchaseReturn) == 0 && sizeof($isPurchase) == 0 && sizeof($hasExpiryStock)==0){
                    $purchasedItems = PurchasedItem::where("purchase_id", "=", $id)->get();
                    foreach ($purchasedItems as $p) {                        
                        $item = Item::find($p->item_id);
                        $item->stock = $item->stock - $p->qty;
                        $item->save();        
                        $deleteItem = Stock::where("type_id","=",$id)
                        ->where("type","=", 2)
                        ->where("item_id","=", $p->item_id)
                        ->delete();        
                        $p->delete();
                    }

                    (!is_null($purchase)) ?  ($r = $purchase->delete()) : ($r = null);

                    if($r){

                        $res = array('status' =>  "success", "data" =>array(), "message" =>trans("validation.success_delete",["attribute" => trans('glob.purchase') ]));
                    }
                    else{
                        $res = array('status' =>  "error", "data" =>array(), "message" => trans("validation.error_delete",["attribute" => trans('glob.purchase') ]));
                    }
                }else{
                    if(sizeof($isPurchaseReturn) > 0){
                       $res = array('status' =>  "warning", "data" =>array(), "message" => "Purchase have Purchase Return, Cant delete Purchase!");
                   }else if(sizeof($hasPaymentDone) > 0){
                      $res = array('status' =>  "warning", "data" =>array(), "message" => "Once payment done of credit purchase can't be deleted!");
                  }else if(sizeof($isPurchase) > 0){
                      $res = array('status' =>  "warning", "data" =>array(), "message" => "Credit purchase can't be deleted!");
                  }
                  else if(sizeof($hasExpiryStock)>0){
                    $res = array('status' =>  "warning", "data" =>array(), "message" => "Purchase have Expiry Items,can't delete Purchase!");
                }

            }
        }

        return response($res, 200);
    }
    catch(Exception $e){
        Log::error("PurchaseController@destroy ".$e);
        return View("errors.exception");

    }
}


public function getPurchaseOrder($id, Request $request){
    try{
        $po = PurchaseOrder::with(['orderItemList','orderItemList.items', 'orderItemList.items.categories'])->find($id);
        $itemList = array();
        $u = array("unit"=>"");
        foreach ($po->orderItemList as $item) {            
            $pitem  = array(
                "item_id" =>  $item->item_id,
                "qty" => $item->quantity,
                "item" => $item->items->with("categories")->find($item->item_id),
                "expiryDate" => "",
                "costPrise" => $item->cost_price,
                "serial_number"=>$item->items->serial_number,
                "totalDiscount"=>0,
                "discount" => 0,
                "unit" => $item->unit,
                "_unit" => $u,
                "amount" => 0,
                "cp_vat"=> $item->items->cp_vat,
            );
            array_push($itemList,$pitem);
        }
        $res = array(
            "status" => 200,
            "purchasedItems"   => $itemList
        );
        if($request->ajax()){
            return Response($res,200);
        }else{
            return View("pages.stock.purchase.purchase",$res);
        }
    }
    catch(Exception $e){
        Log::error("PurchaseController@getPurchaseOrder ".$e);
        return View("errors.exception");

    }

}


public function uploadFile(Request $request){
    try{
        $i=0;
        $file = $request->file('purchase_import');
        $name = $file->getClientOriginalName();
        $items = array();
        $boolIsItemCreate = Common::can_create('item');
        if(file_exists( storage_path() . '/import/' .$name)) {
            $actual_name = pathinfo($name,PATHINFO_FILENAME);
            $original_name = $actual_name;
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            $actual_name = (string)$original_name.$i;
            $name = $actual_name.".".$extension;
            $i++;
            $file->move(storage_path().'/import', $name); 

            $excelData = \App\Legacy\LegacyExcel::load(storage_path().'/import/'. $name, function($reader) {})->get();

            foreach ($excelData as $e) { 
                $it = Item::with(["categories"])->where("barcode", "=", $e->item_code)->get();

                if(!$it->isEmpty()){

                    $it_first = $it->first();
                    $extItem = array(
                        "item_id" => $it_first->id,
                        "serial_number"=>$e->reference_number,
                        "qty" => $e->qty,
                        "item" => $it_first,
                        "unit"  => $this->getUnitId($e->unit),
                        "_unit" => array(
                            "unit" => ""
                        ),
                        "costPrise" => $e->cost_price,
                        'cp_vat'    =>  ($this->intVatType!=0)?$e->cp_vat:0,
                        "salePrise" => $e->selling_price,
                        "vat" => $e->vat,
                        "amount" => 0,
                        "discount" => 0,
                        "totalDiscount" =>0,
                        "type" => "old",
                        "expiryDate" => ""
                    );

                    if($e->vat > 0 && $e->vat <=100){
                        array_push($items, $extItem);
                    }

                    
                }else{
                    if($boolIsItemCreate){
                        $newItem = array(
                            "item_id" => 0,
                            "qty" => $e->qty,
                            "serial_number"=>$e->reference_number,
                            "item" => array(
                                "id" => 0,
                                "barcode" => $e->item_code,

                                "itemName" => $e->item_name,
                                "categories" => array(
                                    "categorie" => $e->sub_category
                                )
                            ),
                            "unit"  => $this->getUnitId($e->unit),
                            "_unit" => array(
                                "unit" => ""
                            ),
                            "costPrise" => $e->cost_price,
                            'cp_vat'    =>  ($this->intVatType!=0)?$e->cp_vat:0,
                            "salePrise" => $e->selling_price,
                            "vat" => $e->vat,
                            "amount" => 0,
                            "discount" => 0,
                            "totalDiscount" =>0,
                            "type" => "new",
                            "expiryDate" => ""
                        );
                        if($e->vat > 0 && $e->vat <=100 && !empty($e->sub_category)){
                            array_push($items, $newItem);
                        }
                    }
                    
                    
                }
            }
            $units = $units = Unit::all();
            $response["status"] = "success";
            $response["items"] = $items;
            $response["units"] = $units;
            return response($response, 200);
        } else {
            $file->move(storage_path().'/import', $name); 

            $excelData = \App\Legacy\LegacyExcel::load(storage_path().'/import/'. $name, function($reader) {})->get();

            foreach ($excelData as $e) {
                $it = Item::with(["categories"])->where("barcode", "=", $e->item_code)->get();



                if(!$it->isEmpty()){
                    $it_first = $it->first();
                    $extItem = array(
                        "item_id" => $it_first->id,
                        "serial_number"=>$e->reference_number,
                        "qty" => $e->qty,
                        "item" => $it_first,
                        "unit"  => $this->getUnitId($e->unit),
                        "_unit" => array(
                            "unit" => ""
                        ),
                        "costPrise" => $e->cost_price,
                        'cp_vat'    => ($this->intVatType!=0)?$e->cp_vat:0,
                        "salePrise" => $e->selling_price,
                        "vat" => $e->vat,
                        "amount" => 0,
                        "discount" => 0,
                        "totalDiscount" =>0,
                        "type" => "old",
                        "expiryDate" => ""
                    );
                    if($e->vat > 0 && $e->vat <=100){
                        array_push($items, $extItem);
                    }
                    
                }else{
                    if($boolIsItemCreate){
                        $newItem = array(
                            "item_id" => 0,
                            "serial_number"=>$e->reference_number,
                            "qty" => $e->qty,
                            "item" => array(
                                "id" => 0,
                                "barcode" => $e->item_code,
                                "itemName" => $e->item_name,
                                "categories" => array(
                                    "categorie" => $e->sub_category
                                )
                            ),
                            "unit"  => $this->getUnitId($e->unit),
                            "_unit" => array(
                                "unit" => ""
                            ),
                            "costPrise" => $e->cost_price,
                            'cp_vat'    =>  ($this->intVatType!=0)?$e->cp_vat:0,
                            "salePrise" => $e->selling_price,
                            "vat" => $e->vat,
                            "amount" => 0,
                            "discount" => 0,
                            "totalDiscount" =>0,
                            "type" => "new",
                            "expiryDate" => ""
                        );
                        if($e->vat > 0 && $e->vat <=100 && !empty($e->sub_category)){
                            array_push($items, $newItem);
                        }
                    }
                    
                    
                }
            }
            $units = $units = Unit::all();
            $response["status"] = "success";
            $response["items"] = $items;
            $response["units"] = $units;
            return response($response, 200);
        }
    }
    catch(Exception $e){
        Log::error("PurchaseController@uploadFile ".$e);
        return View("errors.exception");

    }
}

public function getUnitId($unit){
 try{
    if($unit!=null || $unit!=''){
        $unitObject =  Unit::where("unit","=",$unit)->first();
        if($unitObject['id'] != null){
            return $unitObject['id'];
        }else{
            return $id = \DB::table('units')->insertGetId(
                ['unit' => $unit,'created_at'=> new \DateTime(), 'updated_at'=> new \DateTime() ]
            );
        } 
    }
}
catch(Exception $e){
    Log::error("PurchaseController@getUnitId ".$e);
    return View("errors.exception");

}
}

public function deleteItem($existing,$purchasedItems,$id){
    try{
        foreach ($existing as $existing_item) {
         $flag ='false';
         foreach ($purchasedItems as $p) {              
             if($existing_item->item_id == $p["item"]["id"])
             {               
                $flag ='true';
            }            
        }        

        if($flag == 'false'){          
            $deleteItem = PurchasedItem::where("purchase_id","=",$id)
            ->where("item_id","=", $existing_item->item_id)
            ->delete();
            $item = Item::find($existing_item->item_id);
            $item->stock = $item->stock - $existing_item->qty;
            $item->save();

        } }     
    }
    catch(Exception $e){
        Log::error("PurchaseController@deleteItem ".$e);
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
                "website" => trans('glob.website'), 
                "phone" => trans('glob.phone'),
                "sub_total" => trans('billing.sub_total'),
                'cp_vat_percent' => trans('item.cp_vat_percent'),
                'vat_amt' => trans('glob.vat_amt'),
                'refrence_no'=>trans('item.refrence_number'),
            )
        );
            return $labels;
        }
        catch(Exception $e){
            Log::error("PurchaseController@getLabels ".$e);
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
        }
        catch(Exception $e){
            Log::error("PurchaseController@mallDetails ".$e);
            return View("errors.exception");

        }
    }

    public function getCPVatClub(Request $request){
        try{

            if($this->intVatType!=2){
                $id = $request->all()['data'];
                $returnQty = DB::table('purchases')
                ->leftJoin('purchased_items', 'purchases.id', '=', 'purchased_items.purchase_id')
                ->where('purchases.id','=',$id)
                ->groupBy('purchased_items.cp_vat')
                ->selectRaw('sum( ((purchased_items.costPrise*100)/(100+purchased_items.cp_vat)) * purchased_items.qty) as purchaseAmt , 
                    sum(purchased_items.costPrise * purchased_items.qty) as net_amt , purchased_items.cp_vat as vat_percent , 
                    sum( ((purchased_items.costPrise) - (((purchased_items.costPrise)*100)/(100+(purchased_items.cp_vat)))) * (purchased_items.qty) ) as tax_amt' )
                ->get();

                $labels= $this->getLabels();
                $mallDetails = $this->mallDetails();

                return array(
                    'vatClub'  => $returnQty,
                    "labels" => $labels,
                    "mallDetails" => $mallDetails,
                );
            }

            else{

             $id = $request->all()['data'];
             $returnQty = DB::table('purchases')
             ->leftJoin('purchased_items', 'purchases.id', '=', 'purchased_items.purchase_id')
             ->where('purchases.id','=',$id)
             ->groupBy('purchased_items.cp_vat')
             ->selectRaw('sum(((purchased_items.costPrise) * purchased_items.qty)) as purchaseAmt , 
                sum((((purchased_items.costPrise * purchased_items.cp_vat)/100)*purchased_items.qty) + purchased_items.costPrise * purchased_items.qty ) as net_amt , purchased_items.cp_vat as vat_percent , 
                sum(((purchased_items.costPrise * purchased_items.cp_vat)/100)*purchased_items.qty) as tax_amt' )
             ->get();

             $labels= $this->getLabels();
             $mallDetails = $this->mallDetails();

             return array(
                'vatClub'  => $returnQty,
                "labels" => $labels,
                "mallDetails" => $mallDetails,
            );
         }
     }
     catch(Exception $e){
        Log::error("PurchaseController@getCPVatClub ".$e);
        return View("errors.exception");

    }
}


public function getCatAll(Request $request){
    try{
        return Category::all();
    }
    catch(Exception $e){
        Log::error("PurchaseController@getCatAll ".$e);
        return View("errors.exception");

    }
}
public function getPOList(Request $request){
    try{
        $intSupplierId = $request->all()['data'];
        return $purchaseOrders = PurchaseOrder::whereNotIn("id", function($q){
            $q->select('po_no')->from('purchases')->where("po_no","<>",null);
        })
        ->where('supplier_id','=',$intSupplierId)
        ->get();

    }
    catch(Exception $e){
        Log::error("PurchaseController@getPOList ".$e);
        return View("errors.exception");

    }
}


}
