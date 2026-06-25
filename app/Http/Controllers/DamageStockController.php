<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Toastr;
use Common;

use App\Item;
use App\DamageStock;
use App\DamageStockItem;
use App\User;
use App\Stock;
use Illuminate\Support\Facades\Auth;
use App\ExpiryItems;
use App\Purchase;
use App\Audit;
use App\PurchaseReturn;
use DB;

class DamageStockController extends Controller
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
    public function index(){
        try{

            if (Auth::check())
                {
                    $rollId = \Auth::user()->roleId;
                }

                if($rollId == 1){
                    $damageOrders = DamageStock::with('users')->get();
                }else{
                    $damageOrders = DamageStock::with('users')
                    ->join('users', 'damage_stocks.enterd_by', '=', 'users.id')
                    ->select('damage_stocks.*')
                    ->where('users.roleId', '<>', 1)->get();
                } 

                return View('pages.stock.damage.damage',[
                    'damageOrders' => $damageOrders,
                    'date_format'   => $this->rDateFormat,
                ]);
            }
            catch(Exception $e){
                Log::error("DamageStockController@index ".$e);
                return View("errors.exception");
            }
        }
        public function ajaxindex(){ 
            try{

                if (Auth::check())
                    {
                        $rollId = \Auth::user()->roleId;
                    }

                    if($rollId == 1){
                        $users = User::all();
                    }else{
                        $users = User::where('roleId', '<>', 1)->get();
                    }  

                    $newId = Common::get_autoIncremenetVal('damage_stocks');
                    
                    return Response(['newId' => $newId,'users'  => $users ]);
                }
                catch(Exception $e){
                    Log::error("DamageStockController@ajaxindex ".$e);
                    return View("errors.exception");
                }
            }

            

            public function ajaxstore(Request $request){
                try{

                    $type = 0;

                    $inputData = $request->all()["data"];
                    $type = $inputData['type'];

                    if($type == 0){

                        $orderInsert = DamageStock::create($inputData);

                        for($i = 0; $i < sizeof($inputData["itemList"]); $i++){

                            $item = Item::find($inputData["itemList"][$i]["item_id"]);
                            $item->stock = $item->stock - $inputData["itemList"][$i]["qty"];
                            $item->save();

                            $damageItem = DamageStockItem::create(array(
                                "damage_order_id" => $orderInsert["id"],
                                "item_id" => $inputData["itemList"][$i]["item_id"],
                                "quantity" => $inputData["itemList"][$i]["qty"],
                                "rate" => $inputData["itemList"][$i]["rate"],
                                "amt" => $inputData["itemList"][$i]["amt"],
                                "remark" => $inputData["itemList"][$i]["remark"],
                            ));

                            Stock::create(array(
                                "item_id" => $inputData["itemList"][$i]["item_id"], 
                                "type" => 5, 
                                "type_id" => $orderInsert["id"], 
                                "stock" => $item->stock, 
                                "date"  =>$orderInsert["slip_date"]                        
                            ));

                        }
                        $response["status"] ="success";
                        $response["message"] = trans("validation.success_store",["attribute" => trans('glob.damage_stock') ]);
                        $response["data"] = $orderInsert;
                        return Response($response, 200);

                    }else if($type == 1){
                        $orderInsert = DamageStock::create($inputData);

                        for($i = 0; $i < sizeof($inputData["itemList"]); $i++){

                            $item = Item::find($inputData["itemList"][$i]["item_id"]);
                            $item->stock = $item->stock - $inputData["itemList"][$i]["qty"];
                            $item->save();

                            $expiryItem = ExpiryItems::create(array(
                                "expiry_order_id" => $orderInsert["id"],
                                "mrn_no"  => $inputData["itemList"][$i]["mrn_no"],
                                "item_id" => $inputData["itemList"][$i]["item_id"],
                                "quantity" => $inputData["itemList"][$i]["qty"],
                                "rate" => $inputData["itemList"][$i]["rate"],
                                "amt" => $inputData["itemList"][$i]["amt"],
                                "remark" => $inputData["itemList"][$i]["remark"],
                            ));


                            Stock::create(array(
                                "item_id" => $inputData["itemList"][$i]["item_id"], 
                                "type" => 10, 
                                "type_id" => $orderInsert["id"], 
                                "stock" => $item->stock, 
                                "date"  =>$orderInsert["slip_date"]                        
                            ));

                        }
                        $response["status"] ="success";
                        $response["message"] = trans("validation.success_store",["attribute" => trans('glob.expiry_stock') ]);
                        $response["data"] = $orderInsert;
                        return Response($response, 200);
                    }
                }
                catch(Exception $e){
                    Log::error("DamageStockController@ajaxstore ".$e);
                    return View("errors.exception");
                }
                
            }



            public function ajaxedit($id){
                try{
                 
                    $editDamageStock = DamageStock::with(["damageItemList", 'damageItemList.items','expiryItemList.items'])->find($id);

                    if(count($editDamageStock['expiryItemList'])){

                        foreach ($editDamageStock['expiryItemList'] as  $getitems) {
                            
                            $getPurchaseItemCost = DB::select("SELECT IFNULL((pi.costPrise), 0) as costPrise FROM expiry_items AS ei JOIN ( purchases AS p JOIN purchased_items AS pi ON p.id = pi.purchase_id ) ON ei.mrn_no = p.id AND p.id = pi.purchase_id WHERE pi.purchase_id = '".$getitems['mrn_no']."' AND pi.item_id = '".$getitems['item_id']."' ");
                            
                            $getitems->items->costPrise = $getPurchaseItemCost[0]->costPrise;
                        }

                    }
                    return Response([ 'editDamageStock'  => $editDamageStock ]);        
                }
                catch(Exception $e){
                    Log::error("DamageStockController@ajaxedit ".$e);
                    return View("errors.exception");
                }
            }

            

            public function ajaxupdate(Request $request, $id){

               try{
                /** check user auth */
                if (Auth::check()){
                    $userId = \Auth::id();
                }else{
                    $userId=0;
                }

                $inputData = $request->all()["data"];

                $damageUpdate = DamageStock::find($id);


                if($damageUpdate->type == 0 && $damageUpdate->type == $inputData['type'] ){

                    $existing = DamageStockItem::where("damage_order_id","=",$id)->get();  

                    if(!is_null($damageUpdate)){


                        $this->deleteItem($existing,$inputData,$id,0);  

                        $damageUpdate->fill($request->all()["data"])->save();

                        for($i = 0; $i < sizeof($inputData["itemList"]); $i++){

                            $damageItems_ = DamageStockItem::where("damage_order_id","=",$damageUpdate["id"])
                            ->where("item_id","=", $inputData["itemList"][$i]["item_id"]);

                            $expiredItem = $damageItems_->get();



                            if(!is_null($expiredItem)){
                                $prevQty = $expiredItem->first()["quantity"];
                                $damageItems_->delete();
                            }
                            else{
                                $prevQty = 0;
                            }


                            $item = Item::find($inputData["itemList"][$i]["item_id"]);
                            $item->stock = $item->stock + $prevQty - $inputData["itemList"][$i]["qty"];
                            $item->save();

                            $damageItem = DamageStockItem::create(array(
                                "damage_order_id" => $damageUpdate["id"],
                                "item_id" => $inputData["itemList"][$i]["item_id"],
                                "quantity" => $inputData["itemList"][$i]["qty"],
                                "rate" => $inputData["itemList"][$i]["rate"],
                                "amt" => $inputData["itemList"][$i]["amt"],
                                "remark" => $inputData["itemList"][$i]["remark"],
                            ));

                            if($prevQty != $inputData["itemList"][$i]["qty"]){

                                $stock_ = Stock::where("type_id","=",$damageUpdate["id"])
                                ->where("item_id","=", $inputData["itemList"][$i]["item_id"])
                                ->where("type","=", 5);
                                $stockDetails = $stock_->get();

                                if(!$stockDetails->isEmpty()){
                                    $stock_->update(['stock' => $item->stock]); 
                                }else{

                                    Stock::create(array(
                                        "item_id" =>$inputData["itemList"][$i]["item_id"], 
                                        "type" => 5, 
                                        "type_id" => $damageUpdate["id"], 
                                        "stock" => $item->stock, 
                                        "date"  => $damageUpdate["slip_date"]                       
                                    ));

                                }
                            }
                        }

                        /** save audit for the damage with type id 6 @date->12-07-2017*/
                        $auditSave = Audit::create(array(
                            "record_id" => $id,
                            "type" =>  6,
                            "remark" =>  $inputData['remark'],
                            "user_id" => $userId,
                        ));

                        $response["status"] ="success";
                        $response["message"] = trans("validation.success_update",[ "attribute" => trans('glob.damage_stock') ]);
                        return Response($response, 200);  

                    }

                }else if($damageUpdate->type == 1 && $damageUpdate->type == $inputData['type']){
                    $existing = ExpiryItems::where("expiry_order_id","=",$id)->get();  

                    if(!is_null($damageUpdate)){

                        $this->deleteItem($existing,$inputData,$id,1);  

                        $damageUpdate->fill($request->all()["data"])->save();

                        for($i = 0; $i < sizeof($inputData["itemList"]); $i++){

                            $damageItems_ = ExpiryItems::where("expiry_order_id","=",$damageUpdate["id"])
                            ->where("item_id","=", $inputData["itemList"][$i]["item_id"]);

                            $expiredItem = $damageItems_->get();

                            if(!is_null($expiredItem)){
                                $prevQty = $expiredItem->first()["quantity"];
                                $damageItems_->delete();
                            }
                            else{
                                $prevQty = 0;
                            }


                            $item = Item::find($inputData["itemList"][$i]["item_id"]);
                            $item->stock = $item->stock + $prevQty - $inputData["itemList"][$i]["qty"];
                            $item->save();

                            $damageItem = ExpiryItems::create(array(
                                "expiry_order_id" => $damageUpdate["id"],
                                "mrn_no"  => $inputData["itemList"][$i]["mrn_no"],
                                "item_id" => $inputData["itemList"][$i]["item_id"],
                                "quantity" => $inputData["itemList"][$i]["qty"],
                                "rate" => $inputData["itemList"][$i]["rate"],
                                "amt" => $inputData["itemList"][$i]["amt"],
                                "remark" => $inputData["itemList"][$i]["remark"],
                            ));

                            if($prevQty != $inputData["itemList"][$i]["qty"]){

                                $stock_ = Stock::where("type_id","=",$damageUpdate["id"])
                                ->where("item_id","=", $inputData["itemList"][$i]["item_id"])
                                ->where("type","=", 10);
                                $stockDetails = $stock_->get();

                                if(!$stockDetails->isEmpty()){
                                    $stock_->update(['stock' => $item->stock]); 
                                }else{

                                    Stock::create(array(
                                        "item_id" =>$inputData["itemList"][$i]["item_id"], 
                                        "type" => 10, 
                                        "type_id" => $damageUpdate["id"], 
                                        "stock" => $item->stock, 
                                        "date"  => $damageUpdate["slip_date"]                       
                                    ));
                                }
                            }
                        }

                        /** save audit for the expiry with type id 5 @date->12-07-2017*/
                        $auditSave = Audit::create(array(
                            "record_id" => $id,
                            "type" =>  5,
                            "remark" =>  $inputData['remark'],
                            "user_id" => $userId,
                        ));

                        $response["status"] ="success";
                        $response["message"] = trans("validation.success_update",[ "attribute" => trans('glob.expiry_stock') ]);
                        return Response($response, 200);  

                    }
                }else if( $damageUpdate->type != $inputData['type']){
                    /** delete existing bill and create new because you change damage to expiry or expiry to damage */
                    $this->destroyExisting($id);
                    $damageUpdate->fill($request->all()["data"])->save();

                    $this->createNewBill($id,$inputData);

                    $response["status"] ="success";
                    if($damageUpdate->type == 0){
                        $resourceAlert = trans('glob.damage_stock');
                    }else if($damageUpdate->type == 1){
                     $resourceAlert = trans('glob.expiry_stock');
                 }
                 $response["message"] = trans("validation.success_update",[ "attribute" => $resourceAlert ]);
                 return Response($response, 200); 

             }else{
                $response["status"] ="error";
                toastr::warning('Warning', trans("validation.warning_update_deleted_item",["attribute" => trans('glob.damage_stock') ]));
                return Response($response, 200);
            }
        }
        catch(Exception $e){
            Log::error("DamageStockController@ajaxupdate ".$e);
            return View("errors.exception");
        }

    }
    /** create new item and stock for new type  damage/expiry after type update */
    public function createNewBill($id,$inputData){
        try{
            $orderInsert = DamageStock::find($id);

            if($orderInsert->type == 0){

                for($i = 0; $i < sizeof($inputData["itemList"]); $i++){

                    $item = Item::find($inputData["itemList"][$i]["item_id"]);
                    $item->stock = $item->stock - $inputData["itemList"][$i]["qty"];
                    $item->save();

                    $damageItem = DamageStockItem::create(array(
                        "damage_order_id" => $orderInsert["id"],
                        "item_id" => $inputData["itemList"][$i]["item_id"],
                        "quantity" => $inputData["itemList"][$i]["qty"],
                        "rate" => $inputData["itemList"][$i]["rate"],
                        "amt" => $inputData["itemList"][$i]["amt"],
                        "remark" => $inputData["itemList"][$i]["remark"],
                    ));

                    Stock::create(array(
                        "item_id" => $inputData["itemList"][$i]["item_id"], 
                        "type" => 5, 
                        "type_id" => $orderInsert["id"], 
                        "stock" => $item->stock, 
                        "date"  =>$orderInsert["slip_date"]                        
                    ));

                }
            }else if($orderInsert->type == 1){
                for($i = 0; $i < sizeof($inputData["itemList"]); $i++){

                    $item = Item::find($inputData["itemList"][$i]["item_id"]);
                    $item->stock = $item->stock - $inputData["itemList"][$i]["qty"];
                    $item->save();

                    $expiryItem = ExpiryItems::create(array(
                        "expiry_order_id" => $orderInsert["id"],
                        "mrn_no"  => $inputData["itemList"][$i]["mrn_no"],
                        "item_id" => $inputData["itemList"][$i]["item_id"],
                        "quantity" => $inputData["itemList"][$i]["qty"],
                        "rate" => $inputData["itemList"][$i]["rate"],
                        "amt" => $inputData["itemList"][$i]["amt"],
                        "remark" => $inputData["itemList"][$i]["remark"],
                    ));


                    Stock::create(array(
                        "item_id" => $inputData["itemList"][$i]["item_id"], 
                        "type" => 10, 
                        "type_id" => $orderInsert["id"], 
                        "stock" => $item->stock, 
                        "date"  =>$orderInsert["slip_date"]                        
                    ));
                }
            }
            return;
        }
        catch(Exception $e){
            Log::error("DamageStockController@createNewBill ".$e);
            return View("errors.exception");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyExisting($id)
    {
        try{
            $damageStock = DamageStock::find($id);

            if($damageStock->type == 0){

                $damageStockItem = DamageStockItem::where('damage_order_id', '=', $id)->get();
                
                foreach ($damageStockItem as $p) {           
                    $item = Item::find($p->item_id);
                    $item->stock = $item->stock + $p->quantity;
                    $item->save();        
                    $deleteItem = Stock::where("type_id","=",$id)
                    ->where("type","=", 5)
                    ->where("item_id","=", $p->item_id)
                    ->delete();

                }
                
                DamageStockItem::where('damage_order_id', '=', $id)->delete();
                

            }else if($damageStock->type == 1){

                $damageStockItem = ExpiryItems::where('expiry_order_id', '=', $id)->get();
                foreach ($damageStockItem as $p) {           
                    $item = Item::find($p->item_id);
                    $item->stock = $item->stock + $p->quantity;
                    $item->save();        
                    $deleteItem = Stock::where("type_id","=",$id)
                    ->where("type","=", 10)
                    ->where("item_id","=", $p->item_id)
                    ->delete();

                }

                ExpiryItems::where('expiry_order_id', '=', $id)->delete();
                
            }
            return;
        }
        catch(Exception $e){
            Log::error("DamageStockController@destroyExisting ".$e);
            return View("errors.exception");
        }
    }

    
    /**
     * ajaxdelete function for delete recorde 
     * @param  int $id id for which recore delete
     * @return responce 
     */
    public function ajaxdelete($id)
    {
        try{
            $damageStock = DamageStock::find($id);

            if($damageStock->type == 0){

                $damageStockItem = DamageStockItem::where('damage_order_id', '=', $id)->get();
                
                foreach ($damageStockItem as $p) {           
                    $item = Item::find($p->item_id);
                    $item->stock = $item->stock + $p->quantity;
                    $item->save();        
                    $deleteItem = Stock::where("type_id","=",$id)
                    ->where("type","=", 5)
                    ->where("item_id","=", $p->item_id)
                    ->delete();

                }
                
                DamageStockItem::where('damage_order_id', '=', $id)->delete();
                $damageStock->delete();
                $response["status"] ="success";
                $response["message"] =trans("validation.success_delete",["attribute" => trans('glob.damage_stock') ]);
                

            }else if($damageStock->type == 1){

                $damageStockItem = ExpiryItems::where('expiry_order_id', '=', $id)->get();
                foreach ($damageStockItem as $p) {           
                    $item = Item::find($p->item_id);
                    $item->stock = $item->stock + $p->quantity;
                    $item->save();        
                    $deleteItem = Stock::where("type_id","=",$id)
                    ->where("type","=", 10)
                    ->where("item_id","=", $p->item_id)
                    ->delete();

                }

                ExpiryItems::where('expiry_order_id', '=', $id)->delete();
                $damageStock->delete();
                $response["status"] ="success";
                $response["message"] =trans("validation.success_delete",["attribute" => trans('glob.expiry_stock') ]);

            }



            return Response($response,200);
        }
        catch(Exception $e){
            Log::error("DamageStockController@ajaxdelete ".$e);
            return View("errors.exception");
        }
    }

    public function deleteItem($existing,$inputData,$id, $type){
        try{
            if($type == 0){
                foreach ($existing as $existing_item) {
                    $flag ='false';
                    foreach ($inputData["itemList"] as $p) {

                        if($existing_item->item_id == $p["item_id"]){               
                            $flag ='true';
                        }            
                    }        

                    if($flag == 'false'){         
                        $deleteItem = DamageStockItem::where("damage_order_id","=",$id)
                        ->where("item_id","=", $existing_item->item_id)
                        ->delete();
                        $item = Item::find($existing_item->item_id);
                        $item->stock = $item->stock + $existing_item->quantity;
                        $item->save();

                        $deleteStock = Stock::where("type_id","=",$id)
                        ->where("type","=", 5)
                        ->where("item_id","=", $existing_item->item_id)
                        ->delete();
                    } 
                } 
            }else if($type == 1){
                foreach ($existing as $existing_item) {
                    $flag ='false';
                    foreach ($inputData["itemList"] as $p) {

                        if($existing_item->item_id == $p["item_id"]){               
                            $flag ='true';
                        }            
                    }        

                    if($flag == 'false'){         
                        $deleteItem = ExpiryItems::where("expiry_order_id","=",$id)
                        ->where("item_id","=", $existing_item->item_id)
                        ->delete();
                        $item = Item::find($existing_item->item_id);
                        $item->stock = $item->stock + $existing_item->quantity;
                        $item->save();

                        $deleteStock = Stock::where("type_id","=",$id)
                        ->where("type","=", 10)
                        ->where("item_id","=", $existing_item->item_id)
                        ->delete();
                    } 
                } 
            }
        }
        catch(Exception $e){
            Log::error("DamageStockController@deleteItem ".$e);
            return View("errors.exception");
        }

    }

    public function getItemsByMRN(Request $request, $id){
        try{

            $getpurchase = Purchase::with(['purchasedItems', 'purchasedItems.item'])->find($id);

            $arritems = array();

            if(count($getpurchase)){


                foreach ($getpurchase['purchasedItems'] as  $getitems) {

                    $returnItemQty=DB::select("SELECT IFNULL(SUM(pri.qty), 0) AS qtyReturn FROM purchases AS p LEFT JOIN ( purchase_returns AS pr JOIN purchase_return_items AS pri ON pr.id = pri.purchase_return_id ) ON p.supplier_id = pr.supplier_id AND p.supplierBillNo = pr.supplier_billno WHERE p.supplier_id = '".$getpurchase->supplier_id."' AND p.supplierBillNo = '".$getpurchase->supplierBillNo."' AND pri.item_id = '".$getitems['item_id']."'");
                    $expiryItemQty=DB::select("SELECT IFNULL(SUM(ei.quantity), 0) AS qtyExpiry FROM purchases AS p LEFT JOIN expiry_items AS ei ON p.id = ei.mrn_no WHERE p.id = '".$getpurchase->id."' AND ei.item_id = '".$getitems['item_id']."'");


                    $getitems['item']['stock'] =  $getitems['qty'] - ($returnItemQty[0]->qtyReturn + $expiryItemQty[0]->qtyExpiry);
                    $getitems['item']['costPrise'] = $getitems->costPrise;
                    array_push($arritems, $getitems['item']);
                }


                $response["status"] ="success";
                $response["getpurchase"] = $getpurchase;
                $response["items"] = $arritems;
                return Response($response,200);
            }else{
                $response["status"] ="error";
                $response["getpurchase"] = [];
                $response["items"] = [];
                return Response($response,200);
            }
        }
        catch(Exception $e){
            Log::error("DamageStockController@getItemsByMRN ".$e);
            return View("errors.exception");
        }

    } 
}
