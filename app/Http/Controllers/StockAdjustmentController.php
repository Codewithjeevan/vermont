<?php

namespace App\Http\Controllers;
use Toastr;
use Common;

use Illuminate\Http\Request;
use App\Item;
use App\StockAdjustment;
use App\StockAdjustmentItem;
use App\Stock;
use DB;
use App\Carton;
use App\OnlyUnit;
use App\Unit;

class StockAdjustmentController extends Controller
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
    public function index(Request $request){
        try{
          $stockAdjustmentData = StockAdjustment::all();
          $units = Unit::all(); 
          $newId = Common::get_autoIncremenetVal('stock_adjustments');      

          $labels = $this->getLabels();
          $mallDetails = $this->mallDetails();

          $arrAdjustThrough[] = (object) array('id'=>'1','name' => 'Excess');
          $arrAdjustThrough[] = (object) array('id'=>'2','name' => 'Shortage');
          $arrAdjustThrough[] = (object) array('id'=>'3','name' => 'Wastage');

          $res = array(
            'stockAdjustmentData' => $stockAdjustmentData,
            'units' => $units,
            'date_format'   => $this->rDateFormat,
            'newId' => $newId,
            'labels' => $labels,
            'mallDetails' => $mallDetails,
            'arrAdjustThrough' => $arrAdjustThrough,

            );     
          /*return View('pages.stock.stock_adjustment.stock_adjustment',[
            'stockAdjustmentData' => $stockAdjustmentData,
            "units" => $units,
            'date_format'   => $this->rDateFormat,
        ]);*/

        if($request->ajax()){
            return Response($res,200);
        }else{
            return View('pages.stock.stock_adjustment.stock_adjustment', $res);
        }

      }
      catch(Exception $e){
        Log::error("StockAdjustmentController@index ".$e);
        return View("errors.exception");
    }
}

/*public function ajaxindex(){   
    try{

     $newId = Common::get_autoIncremenetVal('stock_adjustments');
     $units = Unit::all();      

     $labels = $this->getLabels();
     $mallDetails = $this->mallDetails();
     return Response(['newId' => $newId,'labels' => $labels ,"mallDetails" => $mallDetails,"units" => $units,]);
 }
 catch(Exception $e){
    Log::error("StockAdjustmentController@ajaxindex ".$e);
    return View("errors.exception");
}
}*/



public function ajaxstore(Request $request){
    try{

     $inputData = $request->all()["data"];

     $inputData['user_id'] = isset(\Auth::user()->id) ? \Auth::user()->id : 0;     
     $stockAdjustmentInsert = StockAdjustment::create($inputData);

     for($i = 0; $i < sizeof($inputData["itemList"]); $i++){
        $item = Item::find($inputData["itemList"][$i]["item_id"]);

        if($inputData["itemList"][$i]["adjust_through"] == 1){
            $item->stock = $item->stock + ($inputData["itemList"][$i]["qty"]);
        }else if($inputData["itemList"][$i]["adjust_through"] == 2){
            $item->stock = $item->stock - ($inputData["itemList"][$i]["qty"]);
        }else if($inputData["itemList"][$i]["adjust_through"] == 3){
            $item->stock = $item->stock - ($inputData["itemList"][$i]["qty"]);
        }

        //$item->stock = $item->stock - ($inputData["itemList"][$i]["qty"] * $intUnitQty->first()->qty);
        $item->save();

        $stockAdjustmentItem = StockAdjustmentItem::create(array(
            "stock_adjustment_id" => $stockAdjustmentInsert["id"],
            "item_id" => $inputData["itemList"][$i]["item_id"],
            "quantity" => $inputData["itemList"][$i]["qty"],
            "rate" => $inputData["itemList"][$i]["rate"],
            "amt" => $inputData["itemList"][$i]["amt"],
            "adjust_through" => $inputData["itemList"][$i]["adjust_through"],
        ));

        Stock::create(array(
            "item_id" => $inputData["itemList"][$i]["item_id"], 
            "type" => 18, 
            "type_id" => $stockAdjustmentInsert["id"], 
            "stock" => $item->stock, 
            "date"  =>$stockAdjustmentInsert["stock_adjustment_date"]                        
        )); 


    }
    $response["status"] ="success";
    $response["message"] = trans("validation.success_store",["attribute" => trans('glob.stock_adjustment') ]);
    $response["data"] = $stockAdjustmentInsert;
    return Response($response, 200);
}
catch(Exception $e){
    Log::error("StockAdjustmentController@ajaxstore ".$e);
    return View("errors.exception");
}
}




public function ajaxedit($id){
    try{

        $editStockAdjustment = StockAdjustment::with(["stockAdjustmentItemList", 'stockAdjustmentItemList.items', 'stockAdjustmentItemList.items.units'])->find($id);
        return Response([ 'editStockAdjustment'  => $editStockAdjustment ]);        
    }
    catch(Exception $e){
        Log::error("StockAdjustmentController@ajaxedit ".$e);
        return View("errors.exception");
    }
}



/*public function ajaxupdate(Request $request, $id){
    try{
        $inputData = $request->all()["data"];

        $stockAdjustmentInsert = StockAdjustment::find($id);

        $existing = StockAdjustmentItem::where("stock_adjustments_id","=",$id)->get();  

        if(!is_null($stockAdjustmentInsert)){


            $this->deleteItem($existing,$inputData,$id);            

            $stockAdjustmentInsert->fill($request->all()["data"])->save();

            for($i = 0; $i < sizeof($inputData["itemList"]); $i++){
                
                $stockAdjustmentItems_ = StockAdjustmentItem::where("stock_adjustments_id","=",$stockAdjustmentInsert["id"])
                ->where("item_id","=", $inputData["itemList"][$i]["item_id"]);

                $stock_adjustmentdItem = $stockAdjustmentItems_->get();

                if(!is_null($stock_adjustmentdItem)){
                    $prevQty = $stock_adjustmentdItem->first()["quantity"];
                    $stockAdjustmentItems_->delete();
                }
                else{
                    $prevQty = 0;
                }


                $item = Item::find($inputData["itemList"][$i]["item_id"]);
                $intUnitQty=Unit::where("id","=",$item->unit)->get();
                $item->stock = $item->stock + (($prevQty * $intUnitQty->first()->qty) - ($inputData["itemList"][$i]["qty"] * $intUnitQty->first()->qty));
                $item->save();

                $stockAdjustmentItem = StockAdjustmentItem::create(array(
                    "stock_adjustments_id" => $stockAdjustmentInsert["id"],
                    "item_id" => $inputData["itemList"][$i]["item_id"],
                    "quantity" => $inputData["itemList"][$i]["qty"],
                    "rate" => $inputData["itemList"][$i]["rate"],
                    "amt" => $inputData["itemList"][$i]["amt"],
                    "remark" => $inputData["itemList"][$i]["remark"],
                ));

                if($prevQty != $inputData["itemList"][$i]["qty"]){
                    $stock_ = Stock::where("type_id","=",$stockAdjustmentInsert["id"])
                    ->where("item_id","=", $inputData["itemList"][$i]["item_id"]);
                    $stockDetails = $stock_->get();

                    if(!$stockDetails->isEmpty()){
                        $stock_->update(['stock' => $item->stock]); 
                    }else{

                     Stock::create(array(
                        "item_id" =>$inputData["itemList"][$i]["item_id"], 
                        "type" => 4, 
                        "type_id" => $stockAdjustmentInsert["id"], 
                        "stock" => $item->stock, 
                        "date"  => $stockAdjustmentInsert["slip_date"]                       
                    ));

                 }
             }


         }

         $response["status"] ="success";
         $response["message"] = trans("validation.success_update",[ "attribute" => trans('glob.stock_adjustment') ]);
         return Response($response, 200);  

     }else{
        $response["status"] ="error";
        toastr::warning('Warning', trans("validation.warning_update_deleted_item",["attribute" => trans('glob.stock_adjustment') ]));
        return Response($response, 200); 
    } 
}
catch(Exception $e){
    Log::error("StockAdjustmentController@ajaxupdate ".$e);
    return View("errors.exception");
}
}


public function ajaxdelete($id){
    try{
        $stock_adjustmentStock = StockAdjustment::find($id)->delete();
        $stock_adjustmentStockItem = StockAdjustmentItem::where('stock_adjustments_id', '=', $id)->get();        
        foreach ($stock_adjustmentStockItem as $p) {
            $intUnitQty=Unit::where("id","=",$p->items->unit)->get();
            $item = Item::find($p->item_id);
            $item->stock = $item->stock + ($p->quantity * $intUnitQty->first()->qty);
            $item->save();        
            $deleteItem = Stock::where("type_id","=",$id)
            ->where("type","=", 4)
            ->where("item_id","=", $p->item_id)
            ->delete(); 

        }
        StockAdjustmentItem::where('stock_adjustments_id', '=', $id)->delete();
        $response["status"] ="success";
        $response["message"] =trans("validation.success_delete",["attribute" => trans('glob.stock_adjustment') ]);
        $response["data"] = $stock_adjustmentStock;
        return Response($response,200);
    }
    catch(Exception $e){
        Log::error("StockAdjustmentController@ajaxdelete ".$e);
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
            $deleteItem = StockAdjustmentItem::where("stock_adjustments_id","=",$id)
            ->where("item_id","=", $existing_item->item_id)
            ->delete();
            $deleteItem = Stock::where("type_id","=",$id)
            ->where("type","=", 4)
            ->where("item_id","=", $existing_item->item_id)
            ->delete(); 
            $item = Item::find($existing_item->item_id);
            $intUnitQty=Unit::where("id","=",$item->unit)->get();
            $item->stock = $item->stock + ($existing_item->quantity * $intUnitQty->first()->qty);
            $item->save();

        }
         }
    }
        catch(Exception $e){
            Log::error("StockAdjustmentController@ajaxdelete ".$e);
            return View("errors.exception");
        }     
    }*/

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
                    "slip_no" => trans('stock.slip_no'),
                    "slip_date" => trans('stock.slip_date'),
                    "approx_value_of" => trans('stock.approx_value_of'),
                    "display_slip" => trans('glob.display_slip'),
                    "website" => trans('glob.website'), 
                    "phone" => trans('glob.phone'),
                )
            );
                return $labels;
            }
            catch(Exception $e){
                Log::error("StockAdjustmentController@getLabels ".$e);
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
            Log::error("StockAdjustmentController@mallDetails ".$e);
            return View("errors.exception");
        }
    }
    }
