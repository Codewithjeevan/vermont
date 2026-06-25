<?php

namespace App\Http\Controllers;
use Toastr;
use Common;

use Illuminate\Http\Request;
use App\Item;
use App\IssueStock;
use App\IssueStockItem;
use App\Stock;
use DB;

class IssueStockController extends Controller
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
          $issueData = IssueStock::all();      
          return View('pages.stock.issue.issue',[
            'issueData' => $issueData,
            'date_format'   => $this->rDateFormat,
        ]);
      }
      catch(Exception $e){
        Log::error("IssueStockController@index ".$e);
        return View("errors.exception");
    }
}

public function ajaxindex(){   
    try{

     $newId = Common::get_autoIncremenetVal('issue_stocks');

     $labels = $this->getLabels();
     $mallDetails = $this->mallDetails();
     return Response(['newId' => $newId,'labels' => $labels ,"mallDetails" => $mallDetails,]);
 }
 catch(Exception $e){
    Log::error("IssueStockController@ajaxindex ".$e);
    return View("errors.exception");
}
}



public function ajaxstore(Request $request){
    try{

     $inputData = $request->all()["data"];


     $issueInsert = IssueStock::create($inputData);

     for($i = 0; $i < sizeof($inputData["itemList"]); $i++){

        $item = Item::find($inputData["itemList"][$i]["item_id"]);
        $item->stock = $item->stock -$inputData["itemList"][$i]["qty"];
        $item->save();

        $issueItem = IssueStockItem::create(array(
            "issue_stocks_id" => $issueInsert["id"],
            "item_id" => $inputData["itemList"][$i]["item_id"],
            "quantity" => $inputData["itemList"][$i]["qty"],
            "rate" => $inputData["itemList"][$i]["rate"],
            "amt" => $inputData["itemList"][$i]["amt"],
            "remark" => $inputData["itemList"][$i]["remark"],
        ));

        Stock::create(array(
            "item_id" => $inputData["itemList"][$i]["item_id"], 
            "type" => 4, 
            "type_id" => $issueInsert["id"], 
            "stock" => $item->stock, 
            "date"  =>$issueInsert["slip_date"]                        
        )); 


    }
    $response["status"] ="success";
    $response["message"] = trans("validation.success_store",["attribute" => trans('glob.issue') ]);
    $response["data"] = $issueInsert;
    return Response($response, 200);
}
catch(Exception $e){
    Log::error("IssueStockController@ajaxstore ".$e);
    return View("errors.exception");
}
}




public function ajaxedit($id){
    try{

        $editIssueStock = IssueStock::with(["issueItemList", 'issueItemList.items'])->find($id);
        return Response([ 'editIssueStock'  => $editIssueStock ]);        
    }
    catch(Exception $e){
        Log::error("IssueStockController@ajaxedit ".$e);
        return View("errors.exception");
    }
}



public function ajaxupdate(Request $request, $id){
    try{
        $inputData = $request->all()["data"];

        $issueInsert = IssueStock::find($id);

        $existing = IssueStockItem::where("issue_stocks_id","=",$id)->get();  

        if(!is_null($issueInsert)){


            $this->deleteItem($existing,$inputData,$id);            

            $issueInsert->fill($request->all()["data"])->save();

            for($i = 0; $i < sizeof($inputData["itemList"]); $i++){

                $issueItems_ = IssueStockItem::where("issue_stocks_id","=",$issueInsert["id"])
                ->where("item_id","=", $inputData["itemList"][$i]["item_id"]);

                $issuedItem = $issueItems_->get();

                if(!is_null($issuedItem)){
                    $prevQty = $issuedItem->first()["quantity"];
                    $issueItems_->delete();
                }
                else{
                    $prevQty = 0;
                }


                $item = Item::find($inputData["itemList"][$i]["item_id"]);
                $item->stock = $item->stock + $prevQty - $inputData["itemList"][$i]["qty"];
                $item->save();

                $issueItem = IssueStockItem::create(array(
                    "issue_stocks_id" => $issueInsert["id"],
                    "item_id" => $inputData["itemList"][$i]["item_id"],
                    "quantity" => $inputData["itemList"][$i]["qty"],
                    "rate" => $inputData["itemList"][$i]["rate"],
                    "amt" => $inputData["itemList"][$i]["amt"],
                    "remark" => $inputData["itemList"][$i]["remark"],
                ));

                if($prevQty != $inputData["itemList"][$i]["qty"]){
                    $stock_ = Stock::where("type_id","=",$issueInsert["id"])
                    ->where("item_id","=", $inputData["itemList"][$i]["item_id"]);
                    $stockDetails = $stock_->get();

                    if(!$stockDetails->isEmpty()){
                        $stock_->update(['stock' => $item->stock]); 
                    }else{

                     Stock::create(array(
                        "item_id" =>$inputData["itemList"][$i]["item_id"], 
                        "type" => 4, 
                        "type_id" => $issueInsert["id"], 
                        "stock" => $item->stock, 
                        "date"  => $issueInsert["slip_date"]                       
                    ));

                 }
             }


         }

         $response["status"] ="success";
         $response["message"] = trans("validation.success_update",[ "attribute" => trans('glob.issue') ]);
         return Response($response, 200);  

     }else{
        $response["status"] ="error";
        toastr::warning('Warning', trans("validation.warning_update_deleted_item",["attribute" => trans('glob.issue') ]));
        return Response($response, 200); 
    } 
}
catch(Exception $e){
    Log::error("IssueStockController@ajaxupdate ".$e);
    return View("errors.exception");
}
}


public function ajaxdelete($id){
    try{
        $issueStock = IssueStock::find($id)->delete();
        $issueStockItem = IssueStockItem::where('issue_stocks_id', '=', $id)->get();        
        foreach ($issueStockItem as $p) {
            $item = Item::find($p->item_id);
            $item->stock = $item->stock + $p->quantity;
            $item->save();        
            $deleteItem = Stock::where("type_id","=",$id)
            ->where("type","=", 4)
            ->where("item_id","=", $p->item_id)
            ->delete(); 

        }
        IssueStockItem::where('issue_stocks_id', '=', $id)->delete();
        $response["status"] ="success";
        $response["message"] =trans("validation.success_delete",["attribute" => trans('glob.issue') ]);
        $response["data"] = $issueStock;
        return Response($response,200);
    }
    catch(Exception $e){
        Log::error("IssueStockController@ajaxdelete ".$e);
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
            $deleteItem = IssueStockItem::where("issue_stocks_id","=",$id)
            ->where("item_id","=", $existing_item->item_id)
            ->delete();
            $deleteItem = Stock::where("type_id","=",$id)
            ->where("type","=", 4)
            ->where("item_id","=", $existing_item->item_id)
            ->delete(); 
            $item = Item::find($existing_item->item_id);
            $item->stock = $item->stock + $existing_item->quantity;
            $item->save();

        }
         }
    }
        catch(Exception $e){
            Log::error("IssueStockController@ajaxdelete ".$e);
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
                Log::error("IssueStockController@getLabels ".$e);
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
            Log::error("IssueStockController@mallDetails ".$e);
            return View("errors.exception");
        }
    }
    }
