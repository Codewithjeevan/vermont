<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
use App\Category;
use App\Unit;
use App\Supplier;
use App\PurchaseReturn;
use App\PurchaseReturnItem;
//use App\Salesman;
use App\PurchaseOrder;
use App\Purchase;
use App\Stock;
use App\PurchasedItem;
use DB;
use App\Audit;
use Illuminate\Support\Facades\Auth;

use Common;


class PurchaseReturnsController extends Controller
{
    private $rDateFormat;
    private $intVatType;

    public function __construct(){

        $this->rDateFormat = Common::rDateFormat();
        $this->intVatType= Common::vatType();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       try{
        $categories = Category::all();
        $units = Unit::all();
        $suppliers = Supplier::orderBy("name")->get();
        $purchaseReturns = PurchaseReturn::with("_supplier")->get();
        $getReturnThrough = PurchaseReturn::distinct('return_through')->get(['return_through']);
        //$salesman = Salesman::all();
        $ai = \Common::get_autoIncremenetVal("purchase_returns");
        $supplierPurchases = Purchase::all();

        $mallName = DB::select("SELECT options.value as mallName FROM options where options.type = 'general' and options.key = 'mall_name'");
        //$mallDetails = $this->mallDetails();
        // //$labels = $this->getLabels();
        $res = array(
            //"items"  => $items,
            'categories' => $categories,
            "units" => $units,
            "suppliers" => $suppliers,
            "purchaseReturns" => $purchaseReturns,
           //"salesman" => $salesman,
            "returnNo" => $ai,
            "supplierPurchases" => $supplierPurchases,
            "rThrough"  => $getReturnThrough,
            //"labels" => $labels,
            "mallName"  => (isset($mallName[0]->mallName)) ? $mallName[0]->mallName : '',
            //"mallDetails" => $mallDetails,
            'date_format'   => $this->rDateFormat,
        );
        if($request->ajax()){
            return Response($res,200);
        }else{
            return View("pages.stock.purchase_return.purchase_return",$res);
        }
    }
    catch(Exception $e){
        Log::error("PurchaseReturnsController@index ".$e);
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
            $purchaseReturns = $request->all()["item"];
            $pr = PurchaseReturn::create($purchaseReturns);
            if(!is_null($pr)){
                $purchasReturnItems = $purchaseReturns["purchasedItems"];
                foreach ($purchasReturnItems as $p) {

                    $item = Item::find($p["item"]["id"]);
                    $item->stock = $item->stock - $p["qty"];
                    $item->save();

                    PurchaseReturnItem::create(array(
                        "purchase_return_id" => $pr->id, 
                        "item_id" => $p["item"]["id"], 
                        "qty" => $p["qty"], 
                        "unit" => $p["unit"], 
                        "costPrise"  =>$p["costPrise"],  
                        "cp_vat"=> $p["cp_vat"],
                        "discount" => $p["discount"], 
                        "totalDiscount" => $p["totalDiscount"], 
                        "amount" => $p["amount"]
                    ));

                    Stock::create(array(
                        "item_id" => $p["item"]["id"], 
                        "type" => 3, 
                        "type_id" => $pr->id, 
                        "stock" => $item->stock, 
                        "date"  =>$pr->supplier_billdate                        
                    )); 
                }
                if($this->intVatType!=2){
                    $arrVatSummary= $returnQty = DB::table('purchase_returns')
                    ->leftJoin('purchase_return_items', 'purchase_returns.id', '=', 'purchase_return_items.purchase_return_id')
                    ->where('purchase_returns.id','=',$pr->id)
                    ->groupBy('purchase_return_items.cp_vat')
                    ->selectRaw('sum( ((purchase_return_items.costPrise*100)/(100+purchase_return_items.cp_vat)) * purchase_return_items.qty) as purchaseAmt , sum(purchase_return_items.costPrise * purchase_return_items.qty) as net_amt , purchase_return_items.cp_vat as vat_percent , sum( ((purchase_return_items.costPrise) - (((purchase_return_items.costPrise)*100)/(100+(purchase_return_items.cp_vat)))) * (purchase_return_items.qty) ) as tax_amt' )
                    ->get();
                }
                else{
                    $arrVatSummary= $returnQty = DB::table('purchase_returns')
                    ->leftJoin('purchase_return_items', 'purchase_returns.id', '=', 'purchase_return_items.purchase_return_id')
                    ->where('purchase_returns.id','=',$pr->id)
                    ->groupBy('purchase_return_items.cp_vat')
                    ->selectRaw('sum(((purchase_return_items.costPrise) * purchase_return_items.qty)) as purchaseAmt , 
                        sum(((purchase_return_items.costPrise * purchase_return_items.cp_vat)/100) + purchase_return_items.costPrise * purchase_return_items.qty ) as net_amt , purchase_return_items.cp_vat as vat_percent , 
                        sum(((purchase_return_items.costPrise * purchase_return_items.cp_vat)/100)*purchase_return_items.qty) as tax_amt' )
                    ->get();     
                    
                }

                $mallDetails = $this->mallDetails();
                $labels = $this->getLabels();
                
                $res = array(
                    "status"  => "success",
                    "msg"   => trans("validation.success_store",[ "attribute" => trans('glob.purchase_return') ]),
                    "arrVatSummary" => $arrVatSummary,
                    "mallDetails" => $mallDetails,
                    "labels" => $labels,
                );

            }else{
                $res = array(
                    "status"  => "Error",
                    "msg"   => trans("validation.error_store",[ "attribute" => trans('glob.purchase_return') ]),
                    "arrVatSummary" => [],    
                );
            }

            if($request->ajax()){

                return Response($res,200);

            }else{

                ($res["status"] == "success") 
                ? 
                toastr::success('Success', trans("validation.success_store",[ "attribute" => trans('glob.purchase_return') ]))
                :
                toastr::error('error', trans("validation.error_store",[ "attribute" => trans('glob.purchase_return') ]));
                return View("pages.stock.purchase.purchase",$res);
            }
        }
        catch(Exception $e){
            Log::error("PurchaseReturnsController@store ".$e);
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
            $categories = Category::all();
            $units = Unit::all();
            $suppliers = Supplier::all();
            $purchaseReturns = PurchaseReturn::all();
            $getReturnThrough = PurchaseReturn::distinct('return_through')->get(['return_through']);
            
            $ePurchase =  PurchaseReturn::with(["returnItems.item.categories", "returnItems._unit"])->find($id);
            foreach ($ePurchase['returnItems'] as $items) {
                $items['costPrise'] = ROUND($items['costPrise'],2);
                $items['totalDiscount'] = ROUND($items['totalDiscount'],2);
            }

            $res = array(
            //"items"  => [],
                'categories' => $categories,
                "units" => $units,
                "suppliers" => $suppliers,
                "purchaseReturns" => $purchaseReturns,
                "ePurchases" => $ePurchase,
                "rThrough"  => $getReturnThrough,
            );

            if($request->ajax()){
                return Response($res,200);
            }else{
                return View("pages.stock.purchase.purchase",$res);
            }
        }
        catch(Exception $e){
            Log::error("PurchaseReturnsController@show ".$e);
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

        /** check user auth */
        try{
            if (Auth::check()){
                $userId = \Auth::id();
            }else{
                $userId=0;
            }

            $purchaseReturns = $request->all()["item"];
            $pr = PurchaseReturn::find($id);

            $existing = PurchaseReturnItem::where("purchase_return_id","=", $id)->get();  

            if(!is_null($pr)){
                $pr->fill($purchaseReturns)->save();
            }

            $returnItems = $purchaseReturns["return_items"];

            $this->deleteItem($existing,$returnItems,$id);

            foreach ($returnItems as $p) {

                $returnItems_ = PurchaseReturnItem::where("purchase_return_id","=",$id)
                ->where("item_id","=", $p["item"]["id"]);

                $returnedItem = $returnItems_->get();

                if(!is_null($returnedItem)){
                    $prevQty = $returnedItem->first()["qty"];
                    $returnItems_->delete();
                }
                else{
                    $prevQty = 0;
                }


                $item = Item::find($p["item"]["id"]);
                $item->stock = $item->stock + $prevQty - $p["qty"];
                $item->save();


                PurchaseReturnItem::create(array(
                    "purchase_return_id" => $id, 
                    "item_id" => $p["item"]["id"], 
                    "qty" => $p["qty"], 
                    "unit" => $p["unit"], 
                    "costPrise"  =>$p["costPrise"], 
                    "cp_vat"=>$p["cp_vat"],
                    "discount" => $p["discount"], 
                    "totalDiscount" => $p["totalDiscount"], 
                    "amount" => $p["amount"]
                ));

                if($prevQty != $p["qty"]){

                    $stock_ = Stock::where("type_id","=",$id)
                    ->where("item_id","=", $p["item"]["id"]);
                    $stockDetails = $stock_->get();

                    if(!$stockDetails->isEmpty()){
                        $stock_->update(['stock' => $item->stock]); 
                    }else{

                     Stock::create(array(
                        "item_id" => $p["item"]["id"], 
                        "type" => 3, 
                        "type_id" => $id, 
                        "stock" => $item->stock, 
                        "date"  => $pr->supplier_billdate                        
                    ));

                 }
             }
         }

         /** save audit for the purchase return with type id 4 @date->12-07-2017*/
         $auditSave = Audit::create(array(
            "record_id" => $id,
            "type" =>  4,
            "remark" =>  $purchaseReturns['remark'],
            "user_id" => $userId,
        ));
         if($this->intVatType!=2){
             $arrVatSummary= $returnQty = DB::table('purchase_returns')
             ->leftJoin('purchase_return_items', 'purchase_returns.id', '=', 'purchase_return_items.purchase_return_id')
             ->where('purchase_returns.id','=',$id)
             ->groupBy('purchase_return_items.cp_vat')
             ->selectRaw('sum( ((purchase_return_items.costPrise*100)/(100+purchase_return_items.cp_vat)) * purchase_return_items.qty) as purchaseAmt , sum(purchase_return_items.costPrise * purchase_return_items.qty) as net_amt , purchase_return_items.cp_vat as vat_percent , sum( ((purchase_return_items.costPrise) - (((purchase_return_items.costPrise)*100)/(100+(purchase_return_items.cp_vat)))) * (purchase_return_items.qty) ) as tax_amt' )
             ->get();
         }
         else{
            $arrVatSummary= $returnQty = DB::table('purchase_returns')
            ->leftJoin('purchase_return_items', 'purchase_returns.id', '=', 'purchase_return_items.purchase_return_id')
            ->where('purchase_returns.id','=',$pr->id)
            ->groupBy('purchase_return_items.cp_vat')
            ->selectRaw('sum(((purchase_return_items.costPrise) * purchase_return_items.qty)) as purchaseAmt , 
                sum(((purchase_return_items.costPrise * purchase_return_items.cp_vat)/100) + purchase_return_items.costPrise * purchase_return_items.qty ) as net_amt , purchase_return_items.cp_vat as vat_percent , 
                sum(((purchase_return_items.costPrise * purchase_return_items.cp_vat)/100)*purchase_return_items.qty) as tax_amt' )
            ->get();   
            
        }

        $mallDetails = $this->mallDetails();
        $labels = $this->getLabels();

        $res = array(
            "status"  => "success",
            "msg"   => trans("validation.success_update",[ "attribute" => trans('glob.purchase_return') ]),
            "arrVatSummary" => $arrVatSummary,
            "mallDetails" => $mallDetails,
            "labels" => $labels,
        );

        if($request->ajax()){
            return Response($res,200);
        }else{
            toastr::error('error', trans("validation.error_update",[ "attribute" => trans('glob.purchase_return') ]));
            return View("pages.stock.purchase.purchase",$res);
        }

    }
    catch(Exception $e){
        Log::error("PurchaseReturnsController@update ".$e);
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
            $purchaseReturnItems = PurchaseReturnItem::where("purchase_return_id", "=", $id)->get();
            foreach ($purchaseReturnItems as $p) {
                $item = Item::find($p->item_id);
                $item->stock = $item->stock + $p->qty;
                $item->save();        
                $deleteItem = Stock::where("type_id","=",$id)
                ->where("type","=", 3)
                ->where("item_id","=", $p->item_id)
                ->delete(); 
                $p->delete();
            }

            $purchaseReturn  = PurchaseReturn::find($id);
            
            (!is_null($purchaseReturn)) ?  ($r = $purchaseReturn->delete()) : ($r = null);
            
            if($r){

                $res = array('status' =>  "success", "data" =>array(), "message" =>trans("validation.success_delete",["attribute" => trans('glob.purchase_return') ]));
            }
            else{
                $res = array('status' =>  "error", "data" =>array(), "message" => trans("validation.error_delete",["attribute" => trans('glob.purchase_return') ]));
            }

            return response($res, 200);
        }
        catch(Exception $e){
            Log::error("PurchaseReturnsController@destroy ".$e);
            return View("errors.exception");

        }
    }

    public function getSupplierItems(Request $request ,$id){
        try{
            $PurchasedItems = Purchase::where("supplier_id", "=", $id)
            ->with("purchasedItems.item.categories")
            ->get();


            $items = array();       
            foreach ($PurchasedItems as $p) {
                foreach ($p->purchasedItems as $pi) {            
                    $pi->item['qty'] = $pi->qty;                   
                    array_push($items, $pi->item);

                }
            }

            $res = array(
                "status"  => "success",
                "items" => $items
            );

            if($request->ajax()){
                return Response($res,200);
            }else{
                return View("pages.stock.purchase.purchase",$res);
            }
        }
        catch(Exception $e){
            Log::error("PurchaseReturnsController@getSupplierItems ".$e);
            return View("errors.exception");

        }        
    }


    public function deleteItem($existing,$returnItems,$id){
       try{
           foreach ($existing as $existing_item) {
             $flag ='false';
             foreach ($returnItems as $p) {              
                 if($existing_item->item_id == $p["item"]["id"])
                 {               
                    $flag ='true';
                }            
            }        

            if($flag == 'false'){          
                $deleteItem = PurchaseReturnItem::where("purchase_return_id","=",$id)
                ->where("item_id","=", $existing_item->item_id)
                ->delete();
                $item = Item::find($existing_item->item_id);
                $item->stock = $item->stock + $existing_item->qty;
                $item->save();

            } }   
        }
        catch(Exception $e){
            Log::error("PurchaseReturnsController@deleteItem ".$e);
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
            "return_no" => trans('stock.return_no'),
            "mrn_no" => trans('stock.mrn_no'),
            "receiverd_by" => trans('glob.receiverd_by'),
            "purchase"  => trans('glob.purchase'),
            "mrn_date"  => trans('stock.mrn_date'),
            "barcode" => trans('glob.barcode'),
            "perticulars" => trans('glob.perticulars'),
            "discount" => trans('glob.discount'),
            "bill" => trans('billing.bill'),
            "return_date" => trans('stock.return_date'),
            "return_through" => trans('stock.return_through'),
            "purchase_return" => trans('glob.purchase_return'),
            "e_oe" => trans('glob.e_oe'),
            "purchase_return_note" => trans('glob.purchase_return_note'),
            "auth_sign" => trans('glob.auth_sign'),
            "website" => trans('glob.website'), 
            "phone" => trans('glob.phone'),
            "sub_total" => trans('billing.sub_total'),
            "cp_vat"=>trans('item.cp_vat_percent'),
            'vat_amt' => trans('glob.vat_amt'),
            "refrence_no"=>trans('item.refrence_number'),
        )
    );
        return $labels;
    }
    catch(Exception $e){
        Log::error("PurchaseReturnsController@getLabels ".$e);
        return View("errors.exception");

    }
}

function getItem(Request $request){
  try{

    $objData = $request->all()['data'];

    $strBarcode = (isset($objData['barcode'])) ?  $objData['barcode'] : '';
    $itemName = (isset($objData['itemName']) && $objData['itemName'] != '') ?  $objData['itemName'] : '' ;
    $billid = (isset($objData['billno']) && $objData['billno'] != '') ?  $objData['billno'] : 0;
    $intSupplierId = (isset($objData['supplierId']) && $objData['supplierId'] != '') ?  $objData['supplierId'] : 0;


    $item = PurchasedItem::with(["item", "item.units","item.categories"])
    ->join('purchases', 'purchases.id', '=', 'purchased_items.purchase_id')
    ->join('items', 'items.id', '=', 'purchased_items.item_id')
    ->where('purchases.supplierBillNo','=', $billid )
    ->where('purchases.supplier_id','=', $intSupplierId )
    ->where(function ($query) use ($itemName,$strBarcode){
        $query->where('items.itemName', 'LIKE', $itemName)
        ->orWhere('items.barcode', 'LIKE', $strBarcode);
    })->first();
    

    $returnSumQty = DB::table('purchase_returns')
    ->leftJoin('purchase_return_items', 'purchase_returns.id', '=', 'purchase_return_items.purchase_return_id')
    ->where('purchase_returns.supplier_billno','=',$billid)
    ->where('purchase_returns.supplier_id','=',$intSupplierId)
    ->where('purchase_return_items.item_id','=',$item->item_id)
    ->sum('purchase_return_items.qty');


    $item->qty = (($item->qty) - $returnSumQty); 
    
    return Response(array(
       'item' => $item,
   ),200);
}
catch(Exception $e){
    Log::error("PurchaseReturnsController@getItem ".$e);
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
        Log::error("PurchaseReturnsController@mallDetails ".$e);
        return View("errors.exception");

    }
}

function getMaxQty(Request $request){

    try{
        $objData = $request->all()['data'];
        $supplier = (isset($objData['supplier'])) ?  $objData['supplier'] : 0;
        $billNo = (isset($objData['billNo']) && $objData['billNo'] != '') ?  $objData['billNo'] : '' ;
        $itemId = (isset($objData['itemId']) && $objData['itemId'] != '') ?  $objData['itemId'] : 0;
        $purchaseReturnId = (isset($objData['purchase_return_id']) && $objData['purchase_return_id'] != '') ?  $objData['purchase_return_id'] : 0;
        $maxQtyItem = PurchasedItem::join('purchases', 'purchases.id', '=', 'purchased_items.purchase_id')
        ->where('purchases.supplierBillNo','=', $billNo )
        ->where('purchases.supplier_id','=', $supplier )
        ->where('purchased_items.item_id','=', $itemId )
        ->first();

        $returnSumQty = DB::table('purchase_returns')
        ->leftJoin('purchase_return_items', 'purchase_returns.id', '=', 'purchase_return_items.purchase_return_id')
        ->where('purchase_returns.supplier_billno','=',$billNo)
        ->where('purchase_returns.supplier_id','=',$supplier)
        ->where('purchase_return_items.item_id','=',$itemId)
        ->sum('purchase_return_items.qty');

        $returnQty = DB::table('purchase_returns')
        ->leftJoin('purchase_return_items', 'purchase_returns.id', '=', 'purchase_return_items.purchase_return_id')
        ->where('purchase_returns.id','=',$purchaseReturnId)
        ->where('purchase_return_items.item_id','=',$itemId)
        ->sum('purchase_return_items.qty');
        
        $maxQtyItem->qty = ((($maxQtyItem->qty) +$returnQty) - $returnSumQty);   
        
        if(count($maxQtyItem)){
            $res = array(
                "status"  => "success",
                "maxQty" => $maxQtyItem,

            );
        }else{
            $res = array(
                "status"  => "error",
                "maxQty" => null
            );
        }

        return Response(array(
            'data' => $res,
        ),200);
    }
    catch(Exception $e){
        Log::error("PurchaseReturnsController@getMaxQty ".$e);
        return View("errors.exception");

    }
}

}
