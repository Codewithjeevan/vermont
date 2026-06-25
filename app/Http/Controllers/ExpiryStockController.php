<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Toastr;
use Common;

use App\Item;
use App\ExpiryStock;
use App\ExpiryStockItem;
use App\User;
use App\Stock;


class ExpiryStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
         $expiryOrders = ExpiryStock::with('users')->get();
         return View('pages.stock.expiry.expiry',[
            'expiryOrders' => $expiryOrders,
        ]);
     }
     catch(Exception $e){
        Log::error("ExpiryStockController@index ".$e);
        return View("errors.exception");
    }
}
public function ajaxindex(){   

 try{
     $newId = Common::get_autoIncremenetVal('expiry_stocks');
     $items = Item::all();
     $users = User::all();
     return Response(['items' => $items,'newId' => $newId,'users'  => $users ]);
 }
 catch(Exception $e){
    Log::error("ExpiryStockController@ajaxindex ".$e);
    return View("errors.exception");
}
}



public function ajaxstore(Request $request){
    try{

       $inputData = $request->all()["data"];


       $orderInsert = ExpiryStock::create($inputData);

       for($i = 0; $i < sizeof($inputData["itemList"]); $i++){

        $item = Item::find($inputData["itemList"][$i]["item_id"]);
        $item->stock = $item->stock - $inputData["itemList"][$i]["qty"];
        $item->save();

        $expiryItem = ExpiryStockItem::create(array(
            "expiry_order_id" => $orderInsert["id"],
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
    $response["message"] = trans("validation.success_store",["attribute" => trans('glob.expiry_stock') ]);
    $response["data"] = $orderInsert;
    return Response($response, 200);
}
catch(Exception $e){
    Log::error("ExpiryStockController@ajaxstore ".$e);
    return View("errors.exception");
}
}



public function ajaxedit($id){
    try{
        $items = Item::all();
        $editExpiryStock = ExpiryStock::with(["expiryItemList", 'expiryItemList.items'])->find($id);
        return Response(['items' => $items , 'editExpiryStock'  => $editExpiryStock ]);    
    }
    catch(Exception $e){
        Log::error("ExpiryStockController@ajaxedit ".$e);
        return View("errors.exception");
    }    
}


public function ajaxupdate(Request $request, $id){
    try{
        $inputData = $request->all()["data"];
        
        $expiryUpdate = ExpiryStock::find($id);

        $existing = ExpiryStockItem::where("expiry_order_id","=",$id)->get();  

        if(!is_null($expiryUpdate)){


            $this->deleteItem($existing,$inputData,$id);  

            $expiryUpdate->fill($request->all()["data"])->save();

            for($i = 0; $i < sizeof($inputData["itemList"]); $i++){

                $expiryItems_ = ExpiryStockItem::where("expiry_order_id","=",$expiryUpdate["id"])
                ->where("item_id","=", $inputData["itemList"][$i]["item_id"]);

                $expiredItem = $expiryItems_->get();

                if(!is_null($expiredItem)){
                    $prevQty = $expiredItem->first()["quantity"];
                    $expiryItems_->delete();
                }
                else{
                    $prevQty = 0;
                }


                $item = Item::find($inputData["itemList"][$i]["item_id"]);
                $item->stock = $item->stock + $prevQty - $inputData["itemList"][$i]["qty"];
                $item->save();

                $expiryItem = ExpiryStockItem::create(array(
                    "expiry_order_id" => $expiryUpdate["id"],
                    "item_id" => $inputData["itemList"][$i]["item_id"],
                    "quantity" => $inputData["itemList"][$i]["qty"],
                    "rate" => $inputData["itemList"][$i]["rate"],
                    "amt" => $inputData["itemList"][$i]["amt"],
                    "remark" => $inputData["itemList"][$i]["remark"],
                ));

                if($prevQty != $inputData["itemList"][$i]["qty"]){

                   $stock_ = Stock::where("type_id","=",$expiryUpdate["id"])
                   ->where("item_id","=", $inputData["itemList"][$i]["item_id"]);
                   $stockDetails = $stock_->get();

                   if(!$stockDetails->isEmpty()){
                    $stock_->update(['stock' => $item->stock]); 
                }else{

                   Stock::create(array(
                    "item_id" =>$inputData["itemList"][$i]["item_id"], 
                    "type" => 4, 
                    "type_id" => $expiryUpdate["id"], 
                    "stock" => $item->stock, 
                    "date"  => $expiryUpdate["slip_date"]                       
                ));

               }
           }
       }

       $response["status"] ="success";
       $response["message"] = trans("validation.success_update",[ "attribute" => trans('glob.expiry_stock') ]);
       return Response($response, 200);  

   }else{
    $response["status"] ="error";
    toastr::warning('Warning', trans("validation.warning_update_deleted_item",["attribute" => trans('glob.expiry_stock') ]));
    return Response($response, 200); 
} 
}
catch(Exception $e){
    Log::error("ExpiryStockController@ajaxupdate ".$e);
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
            $expiryStock = ExpiryStock::find($id)->delete();
            $expiryStockItem = ExpiryStockItem::where('expiry_order_id', '=', $id)->get();
            foreach ($expiryStockItem as $p) {
                $item = Item::find($p->item_id);
                $item->stock = $item->stock + $p->quantity;
                $item->save();        
                $deleteItem = Stock::where("type_id","=",$id)
                ->where("type","=", 4)
                ->where("item_id","=", $p->item_id)
                ->delete(); 
                $p->delete();
            }
            $response["status"] ="success";
            $response["message"] =trans("validation.success_delete",["attribute" => trans('glob.expiry_stock') ]);
            $response["data"] = $expiryStock;
            return Response($response,200);
        }
        catch(Exception $e){
            Log::error("ExpiryStockController@ajaxdelete ".$e);
            return View("errors.exception");
        }
    }

    public function deleteItem($existing,$inputData,$id){
        try{
         foreach ($existing as $existing_item) {
           $flag ='false';
           foreach ($inputData["itemList"] as $p) {
              
               if($existing_item->item_id == $p["item_id"])
               {               
                $flag ='true';
            }            
        }        

        if($flag == 'false'){         
            $deleteItem = ExpiryStockItem::where("expiry_order_id","=",$id)
            ->where("item_id","=", $existing_item->item_id)
            ->delete();
            $item = Item::find($existing_item->item_id);
            $item->stock = $item->stock + $existing_item->quantity;
            $item->save();

        } } 
    }  
    catch(Exception $e){
        Log::error("ExpiryStockController@deleteItem ".$e);
        return View("errors.exception");
    }  
}

}
