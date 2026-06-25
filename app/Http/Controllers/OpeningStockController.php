<?php

namespace App\Http\Controllers; 

use Illuminate\Http\Request;
use App\Category;
use App\Unit;
use App\OpeningStock;
use App\Item;
use Toastr;
use Excel;
use PDF;
use Carbon\Carbon;
use App\Stock;
use DB;
use Common;

class OpeningStockController extends Controller
{

    private $intVatType;

    public function __construct(){

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
        $category = Category::all();
        
        $units  = Unit::all();
        //$items = Item::with("categories","units")->get();

        if($request->input('searchkey') !=null ){
            $search = $request->input('searchkey');
            $openStockList = OpeningStock::with(['item'])
            ->join('items', 'items.id', '=', 'opening_stock.itemId')
            ->join('categories', 'items.category', '=', 'categories.id')
            ->select('opening_stock.*', 'items.id as item_id','categories.categorie' )
            ->orWhere('items.itemName','LIKE','%'.$request->input('searchkey').'%')
            ->orWhere('categories.categorie','LIKE','%'.$request->input('searchkey').'%')
            ->paginate(25);
        }else{

            $openStockList = OpeningStock::paginate(25);

        }



        $boolIsCatCreate = Common::can_create('category');
        $boolIsUnitCreate = Common::can_create('unit');
        $boolIsOpenStockCreate = Common::can_create('open_stock');
        $boolIsItemUpdtae = Common::can_update('item');
        $boolIsItemCreate = Common::can_create('item');

        $res = array('openStockList' => $openStockList,
            "catgeories" => $category,
           // "items" => [],
            "units" => $units,
            'boolIsCatCreate'=>$boolIsCatCreate,
            'boolIsUnitCreate'=>$boolIsUnitCreate,
            'boolIsOpenStockCreate'=>$boolIsOpenStockCreate,
            'boolIsItemUpdtae'=>$boolIsItemUpdtae,
            'boolIsItemCreate'=>$boolIsItemCreate,
            'searchkey' => $request->input('searchkey'),
        );

        if($request->ajax()){
            return Response($res,200);
        }else{
            return View("pages.masters.opening_stock_entries.opening_stock",$res);
        }

    }
    catch(Exception $e){
        Log::error("OpeningStockController@index ".$e);
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

            $req =  $request->all()['openStock'];

            foreach ($req as $openStockData) {

                /** assign itemId from item obejct come from opening stockobject  */
                $openStockData['itemId'] = $openStockData['item']['id'];

                $openingStock = OpeningStock::create($openStockData);   

                $data = $request->all();      

                $item = Item::find($openStockData["itemId"]);
                $item->stock = $item->stock + $openStockData["openingStock"];
                $item->save();

                $openStock = Stock::create(array(
                    "item_id" => $openStockData["itemId"], 
                    "type" => 1, 
                    "type_id" => $openingStock->id, 
                    "stock" => $item->stock, 
                    "date"  =>$openStockData["open_stock_date"]                       
                ));

            }

            if(!is_null($openStock)){
                $res = array(
                    "status"  => "success",
                    "msg"   => trans("validation.success_store",[ "attribute" => trans('glob.opening_stock') ])
                );
            }else{
                $res = array(
                    "status"  => "Error",
                    "msg"   => trans("validation.error_store",[ "attribute" => trans('glob.opening_stock') ])    
                );
            }

            if($request->ajax()){
                return Response($res,200);
            }else{
               toastr::success('Success', trans("validation.success_store",[ "attribute" => trans('glob.opening_stock') ]));
               return redirect()->action('OpeningStockController@index');
           }

       }
       catch(Exception $e){
        Log::error("OpeningStockController@store ".$e);
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
            $editOpeningStock= OpeningStock::where('id', $id)->with(['item.categories'])->first(); 

            if($request->ajax()){
                return Response($editOpeningStock,200);
            }else{
                return View('pages.stock.club_barcode.club_barcode',$editOpeningStock);
            }
        }
        catch(Exception $e){
            Log::error("OpeningStockController@show ".$e);
            return View("errors.exception");

        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function edit($id)
    {

        $category = Category::all();
        $items = Item::with("categories","units")->get();

        $units  = Unit::pluck("unit","id");
        $openStockList = OpeningStock::all();
        $editOpenStock= OpeningStock::with("item.categories")->where('id', $id)->first();     

        return View('pages.masters.opening_stock_entries.opening_stock',[
         'editOpenStock'=> $editOpenStock,
         'openStockList' => $openStockList,
         "catgeories" => $category,
         "items" =>[],
         "units" => $units
         ]);
     }*/

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
       try{
        $editOpenStock = OpeningStock::find($id);       

        if(!is_null($editOpenStock)){           
            $data = $request->all()['openStock'];  

            $prevQty = $editOpenStock->openingStock; 

            /**
             * delete prev item if item which is in database and which is in request is not matched
             * so delete prev item from stock 
             * and newly added item to stock
             */
            $prevItem = $editOpenStock->itemId;

            if($prevItem!=$data["itemId"]){

                $objPrevItem = Item::find($prevItem);     
                $objPrevItem->stock = $objPrevItem->stock - $prevQty;
                $objPrevItem->save(); 

                $stockPrevItem = Stock::where("type_id","=",$editOpenStock->id)
                ->where("item_id","=", $prevItem)
                ->where("type","=", 1)->delete();

                $objCurrentItem = Item::find($data["itemId"]);     
                $objCurrentItem->stock = $objCurrentItem->stock + $data["openingStock"];
                $objCurrentItem->save(); 
                
                Stock::create(array(
                    "item_id" => $data["itemId"], 
                    "type" => 1, 
                    "type_id" => $editOpenStock->id, 
                    "stock" => $data["openingStock"], 
                    "date"  =>$data["open_stock_date"]                       
                )); 

            }else{

                $item = Item::find($data["itemId"]);
                $item->stock = $item->stock + $data["openingStock"] - $prevQty;
                
                if($prevQty != $data["openingStock"]){
                    $stock_ = Stock::where("type_id","=",$editOpenStock->id)
                    ->where("item_id","=", $data["itemId"])
                    ->where("type","=", 1);
                    $stockDetails = $stock_->get();

                    if(!$stockDetails->isEmpty()){
                        $stock_->update(['stock' => $item->stock]); 
                    }else{
                        Stock::create(array(
                            "item_id" => $data["itemId"], 
                            "type" => 1, 
                            "type_id" => $editOpenStock->id, 
                            "stock" => $item->stock, 
                            "date"  =>$data["open_stock_date"]                       
                        )); 
                    }
                }

                $item->save();           
            }           
            $editOpenStock->fill($request->all()['openStock'])->save();

            if($request->ajax()){
                return Response([
                    'editOpenStock' => $editOpenStock,
                    'status'    => 'success',
                    'msg'       => trans("validation.success_update",[ "attribute" => trans('glob.opening_stock') ])
                ],200);
            }else{
                toastr::success('Success', trans("validation.success_update",[ "attribute" => trans('glob.open_stock') ]));
                return redirect()->action('OpeningStockController@index');
            }

        }else{
            if($request->ajax()){
                return Response($editOpenStock,200);
            }else{
                toastr::warning('Warning', trans("validation.warning_update_deleted_item",["attribute" => trans('glob.open_stock') ]));
                return redirect()->action('OpeningStockController@index');
            }
        }
    }
    catch(Exception $e){
        Log::error("OpeningStockController@update ".$e);
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
            $openingStock  = OpeningStock::find($id);
            $item = Item::find($openingStock->itemId);
            $item->stock = $item->stock - $openingStock->openingStock;
            $item->save();
            $deleteItem = Stock::where("type_id","=",$id)
            ->where("type","=", 1)
            ->where("item_id","=", $openingStock->itemId)
            ->delete(); 
            
            (!is_null($openingStock)) ?  ($r = $openingStock->delete()) : ($r = null);
            
            if($r){
                $res = array('status' =>  "success", "data" =>array(), "message" => trans("validation.success_delete",["attribute" => trans('glob.open_stock') ]));
            }
            else{
                $res = array('status' =>  "error", "data" =>array(), "message" => trans("validation.error_delete",["attribute" => trans('glob.open_stock') ]));
            }

            return response($res, 200);
        }
        catch(Exception $e){
            Log::error("OpeningStockController@destroy ".$e);
            return View("errors.exception");

        }

    }


    /**
     *get Items
     */

    public function getItem($id){
        try{

         $data = \DB::table('items')->where('category', $id )->get();

         return response($data, 200);
     }
     catch(Exception $e){
        Log::error("OpeningStockController@getItem ".$e);
        return View("errors.exception");

    }
}

/** export to xls OpeningStock list */
public function exportToXls(){
    try{
        $dataList = OpeningStock::all();       
        if(sizeof($dataList)){
            $carbon = Carbon::today();
            $timestamp = $carbon->timestamp;
            $format = $carbon->format('Y-m-d');
            $exportName = 'OpeningStock'.$format;

            Excel::create($exportName, function ($excel) use($dataList){

                $excel->sheet('OpeningStock', function ($sheet) use($dataList) {

                //$dataList = OpeningStock::get();
                //$category = Category::all();
                //$items = Item::pluck('itemName','id');
                //$units  = Unit::pluck("unit","id"); 

                    $itemList = array();


                    foreach ($dataList as $o) {              

                      array_push($itemList, array(
                       trans('glob.id')  => $o->id,
                       trans('open_stock.item_name')  => $o->item->itemName,
                       trans('glob.barcode')  => $o->item->barcode,  
                       trans('glob.sub_category')  => $o->item->categories->categorie, 
                       trans('open_stock.opening_stock')  => " ".\Common::fixQty($o->openingStock), 
                       trans('open_stock.unit')  => $o->units->unit

                   )
                  );
                  }     



                  /* setting column names for data - you can of course set it manually */
                  $sheet->appendRow(array_keys($itemList[0])); /* column name*/

                  /* getting last row number (the one we already filled and setting it to bold */
                      $sheet->row($sheet->getHighestRow(), function ($row) {
                        $row->setFontWeight('bold');
                    });

                      /* putting users data as next rows*/
                      foreach ($itemList as $user) {
                        $sheet->appendRow($user);
                    }
                });

            })->export('xlsx');


            return back();

        }else{
            toastr::warning('Warning', trans("validation.export_error"));
            return back();
        }
    }
    catch(Exception $e){
        Log::error("OpeningStockController@exportToXls ".$e);
        return View("errors.exception");

    }
}
/** export to PDF OpeningStock list */
public function exportToPdf(){
    try{
    //$category = Category::all();
    //$items = Item::pluck('itemName','id');
    //$units  = Unit::pluck("unit","id");
        $data = OpeningStock::all();       
        if(sizeof($data)){
            $pdf = PDF::loadView('pages.masters.opening_stock_entries.openingstock_pdf', [
                'data' => $data, 
            //"catgeories" => $category,
            //"items" => $items,
            //"units" => $units       
            ]); 

            $pdf->setPaper('a4');
            $carbon = Carbon::today();
            $timestamp = $carbon->timestamp;
            $format = $carbon->format('Y-m-d');
            $exportName = 'OpeningStock'.$format.'.pdf';
            return $pdf->download($exportName);

        }else{
            toastr::warning('Warning', trans("validation.export_error"));
            return back();
        }
    }
    catch(Exception $e){
        Log::error("OpeningStockController@exportToPdf ".$e);
        return View("errors.exception");

    }
}

public function uploadFile(Request $request){
    try{
        ini_set("max_execution_time",6000);
        $i=0;
        $file = $request->file('item_stock_import');
        $name = $file->getClientOriginalName();
        $items = array();
        $importedItems = $items;
        $duplicate = array();

        $boolIsCatCreate = Common::can_create('category');

        $boolIsUnitCreate = Common::can_create('unit');

        $boolIsItemUpdate = Common::can_update('item');

        $boolIsItemCreate = Common::can_create('item');

        $boolIsOpeningStockCreate = Common::can_create('open_stock');

        if(file_exists( storage_path() . '/import/' .$name)) {
            $actual_name = pathinfo($name,PATHINFO_FILENAME);
            $original_name = $actual_name;
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            $actual_name = uniqid($original_name.'_');
            $name = $actual_name.".".$extension;
            $i++;
            $file->move(storage_path().'/import', $name); 
        } else {
            $file->move(storage_path().'/import', $name); 
        }
        $excelData = \Excel::load(storage_path().'/import/'. $name, function($reader) {})->get();


        foreach ($excelData as $e) {

            $check = false;
            if($e->item_code!='') { 
              $e->sub_category =  (isset($e->sub_category)) ? trim($e->sub_category) : '';
              $it = Item::with(["categories"])->where("barcode", "=", $e->item_code)->get();

              if(!$it->isEmpty() && $it[0]->categories->categorie == $e->sub_category){

                $it_first = $it->first();

                $conflict_check = \DB::table('items')
                ->join('categories', 'items.category', '=', 'categories.id')
                ->join('units', 'items.unit', '=', 'units.id')
                ->where('categories.categorie','=', trim($e->sub_category))
                ->where('units.unit','=', trim($e->unit))
                ->where("itemName", "=", trim($e->item_name))
                ->where("barcode", "=", trim($e->item_code))
                ->where("barcode", "=", trim($e->reference_number))
                ->get();

                if($conflict_check->isEmpty()){
                    $check = true;
                }else{
                    $check = false;
                }

                $p = array(
                    "id" => $it_first->id, 
                    'category'  => $e->sub_category ,
                    'barcode'  => $e->item_code,  
                    'itemName'  =>  $e->item_name,
                    'serial_number'=>$e->reference_number,
                    'unit'  =>  $e->unit,  
                    'qty'  =>  $e->qty,
                    'costPrice'  =>  $e->cost_price,
                    'cp_vat'=>($this->intVatType!=0)?$e->cp_vat:0,
                    'salePrice'  => $e->selling_price,
                    'vat'  =>($this->intVatType!=0)? $e->vat:0, 
                    'type'  => "old",
                    'conflict'  => $check,
                    'isItemCreate' => 'true',
                );
                $item = Item::find($p["id"]);
                $unittrim=trim($p["unit"]);
                $stringunit=$unittrim !='' ? $unittrim:'PC';
                $unit = Unit::where("unit", "like",$stringunit)->get();

                if($unit->isEmpty()){
                    if($boolIsUnitCreate){
                        $newUnit = Unit::insertGetId(array(
                            "unit" => $p["unit"],
                            "created_at"  => Carbon::now(),
                            "updated_at" => Carbon::now()
                        ));
                    }else{
                        $newUnit = 0;
                    }
                    
                }else{
                    $newUnit = $unit->first()->id;
                }

                
                if($e->vat < 0 || $e->vat >100 || $e->cp_vat < 0 || $e->cp_vat >100){

                    $p["type"] ="vat";
                    array_push($duplicate, $p);
                }else{
                   if( trim($p["itemName"])==''){
                    $p["type"] ="duplicate";
                    array_push($duplicate, $p);
                    continue;
                }
                if(trim($p["serial_number"])==''){
                    $p["type"] ="duplicate";
                    array_push($duplicate, $p);
                    continue;
                }

                if(! is_null($item)){

                    if($boolIsItemUpdate){

                        if($p["qty"] > 0){

                            $newOpenStock = OpeningStock::create(array(
                                'open_stock_date' => Carbon::today(),
                                'itemId' => $item->id, 
                                'openingStock' => $p["qty"],
                                'unit' =>$newUnit,
                                "created_at"  => Carbon::now(),
                                "updated_at" => Carbon::now()
                            ));

                            $item->stock = $item->stock + $newOpenStock["openingStock"];
                            $item->serial_number=$p["serial_number"];
                            $item->costPrise=$p["costPrice"];
                            $item->salePrise=$p["salePrice"];
                            $item->vat=$p["vat"];
                            $item->cp_vat=$p["cp_vat"];
                            $item->unit=$newUnit;
                            $item->save();

                            $stock = Stock::create(array(
                                "item_id" => $item->id, 
                                "type" => 1, 
                                "type_id" => $newOpenStock['id'], 
                                "stock" => $item->stock, 
                                "date"  =>$newOpenStock["open_stock_date"]                       
                            ));

                        }else{
                            $item->serial_number=$p["serial_number"];
                            $item->costPrise=$p["costPrice"];
                            $item->salePrise=$p["salePrice"];
                            $item->vat=$p["vat"];
                            $item->cp_vat=$p["cp_vat"];
                            $item->unit=$newUnit;
                            $item->save();

                        }
                    }
                }
            }

        }else{
            $e->unit = ($e->unit!=null) ? $e->unit : 'PC';
            $unittrim=trim($e->unit);
            $unit = Unit::where("unit", "like",$unittrim)->get();

            if($unit->isEmpty()){
                if($boolIsUnitCreate){
                    $newUnit = Unit::insertGetId(array(
                        "unit" => $e->unit,
                        "created_at"  => Carbon::now(),
                        "updated_at" => Carbon::now()
                    ));
                }else{
                    $newUnit = 0;
                }

            }else{
                $newUnit = $unit->first()->id;
            }
            $p = array(
                "id" => 0, 
                'category'  =>  $e->sub_category,
                'barcode'  =>  $e->item_code,  
                'serial_number'=>$e->reference_number,
                'itemName'  =>  $e->item_name,
                'unit'  => (trim($e->unit)!=null) ? $e->unit : 'PC', 
                'qty'  =>  $e->qty,
                'costPrice'  =>  $e->cost_price,
                'cp_vat'=>($this->intVatType!=0)?$e->cp_vat:0,
                'salePrice'  => $e->selling_price,
                'vat'  => ($this->intVatType!=0)?$e->vat:0,
                'type'  => "new",
                'conflict'  => $check,
                'isItemCreate' => 'true',
            );

            if(!empty($p["category"])){
              $cat = Category::where("parent_category","<>", 0)
              ->where("categorie", "like", $p["category"])
              ->get();

              if($cat->isEmpty()){
                if($boolIsCatCreate){
                    $newCat = Category::insertGetId(array(
                        "categorie" => $p["category"],
                        "parent_category" => 1,
                        "created_at"  => Carbon::now(),
                        "updated_at" => Carbon::now()
                    ));
                }else{
                    $newCat =0;
                }

            }else{
                $newCat = $cat->first()->id;
            }
        }
        else{
         $newCat =0;
     }


     $item_check = Item::where("barcode","=", $p["barcode"])->get();
     if($e->vat < 0 || $e->vat >100 || $e->cp_vat < 0 || $e->cp_vat >100){
        $p["type"] ="vat";
        array_push($duplicate, $p);
    }else{

        if( trim($p["itemName"])==''){
            $p["type"] ="duplicate";
            array_push($duplicate, $p);
            continue;
        }
        if(trim($p["serial_number"])==''){
            $p["type"] ="duplicate";
            array_push($duplicate, $p);
            continue;
        }


        if($item_check->isEmpty()  ){
            if($newCat !=0 && $newUnit!=0){

                if($boolIsItemCreate){

                    $newItem = Item::insertGetId(array(
                        'category' => $newCat,
                        'barcode' => $p["barcode"], 
                        'itemName' => $p["itemName"],
                        'serial_number'=>$p["serial_number"],
                        'costPrise' => $p["costPrice"],
                        'cp_vat'=>$p["cp_vat"],
                        'salePrise' => $p["salePrice"],
                        'vat' => $p["vat"],
                        'unit' => $newUnit,
                        'stock' => 0,
                        "created_at"  => Carbon::now(),
                        "updated_at" => Carbon::now(),
                    ));

                    if($p["qty"] > 0){

                        $newOpenStock = OpeningStock::create(array(
                            'open_stock_date' => Carbon::today(),
                            'itemId' => $newItem, 
                            'openingStock' => $p["qty"],
                            'unit' => $newUnit,
                            "created_at"  => Carbon::now(),
                            "updated_at" => Carbon::now()
                        ));


                        $item = Item::find($newItem);


                        $item->stock = $item->stock + $newOpenStock["openingStock"];

                        $item->save();

                        $openStock = Stock::create(array(
                            "item_id" => $newItem, 
                            "type" => 1, 
                            "type_id" => $newOpenStock['id'], 
                            "stock" => $item->stock, 
                            "date"  =>$newOpenStock["open_stock_date"]                       
                        ));
                    }


                }
            }

        }else{
            $newItem = $item_check->first()->id;
            $p["type"] ="duplicate";
            array_push($duplicate, $p);
        }
    }
}
}
}

$response["status"] = "success";

if(count($duplicate)==0){
    $res = array(
        "status"  => "success",
        "msg"   => trans("validation.success_store",[ "attribute" => trans('glob.item') ]),
    );
}else{
    $res = array(
        "status"  => "warning",
        "msg"   => trans("validation.success_store",[ "attribute" => trans('glob.item') ]),
        "duplicate" => $duplicate,
    );
}

return response($res, 200);
}
catch(Exception $e){
    Log::error("OpeningStockController@uploadFile ".$e);
    return View("errors.exception");

}

}
function saveOpenStock(Request $request){
    try{
        $items = $request->all()["data"];
        $res = $this->saveOpenStock1($items);
        if($request->ajax()){

            return Response($res,200);

        }else{

            ($res["status"] == "success") 
            ? 
            toastr::success('Success', trans("validation.success_store",[ "attribute" => trans('glob.open_stock') ]))
            :
            toastr::error('error', trans("validation.error_store",[ "attribute" => trans('glob.open_stock') ]));
            return View("pages.master.opening_stock_entries.opening_stock",$res);
        }
    }
    catch(Exception $e){
        Log::error("OpeningStockController@saveOpenStock ".$e);
        return View("errors.exception");

    }
}

function saveOpenStock1($items){
    try{


        $importedItems = $items;
        $duplicate = array();
        $arrIsCreate = array();
        

        $boolIsCatCreate = Common::can_create('category');

        $boolIsUnitCreate = Common::can_create('unit');

        $boolIsItemUpdate = Common::can_update('item');

        $boolIsItemCreate = Common::can_create('item');

        if(!is_null($importedItems)){

            foreach ($importedItems as $p) {


                if($p["id"]==0){

                    $cat = Category::where("parent_category","<>", 0)
                    ->where("categorie", "like", trim($p["category"]))
                    ->get();

                    if($cat->isEmpty()){
                        if($boolIsCatCreate){
                            if(!empty($p["category"])){
                                $newCat = Category::insertGetId(array(
                                    "categorie" => $p["category"],
                                    "parent_category" => 1,
                                    "created_at"  => Carbon::now(),
                                    "updated_at" => Carbon::now()
                                ));
                            }
                            else{
                                $newCat=0;
                            }
                        }else{
                            $newCat = 0;
                        }

                    }else{
                        $newCat = $cat->first()->id;
                    }

                    $unit = Unit::where("unit", "like", $p["unit"])->get();

                    if($unit->isEmpty()){

                        if($boolIsUnitCreate){

                            $newUnit = Unit::insertGetId(array(
                                "unit" => $p["unit"],
                                "created_at"  => Carbon::now(),
                                "updated_at" => Carbon::now()
                            ));

                        }else{
                            $newUnit = 0;
                        }
                        
                    }else{
                        $newUnit = $unit->first()->id;
                    }

                    $item_check = Item::where("barcode","=", $p["barcode"])->get();

                    if($item_check->isEmpty()){

                        if($p['vat'] < 0 || $p['vat'] >100 || $p['cp_vat']<0|| $p['cp_vat']>100){
                            $p["type"] ="vat";
                            array_push($duplicate, $p);
                        }else{

                            if( trim($p["itemName"])==''){
                                $p["type"] ="duplicate";
                                array_push($duplicate, $p);
                                continue;
                            }
                            if( trim($p["serial_number"])==''){
                                $p["type"] ="duplicate";
                                array_push($duplicate, $p);
                                continue;
                            }

                            if($newCat!=0 && $newUnit!=0 ){

                                if($boolIsItemCreate){

                                 $newItem = Item::insertGetId(array(
                                    'category' => $newCat,
                                    'barcode' => $p["barcode"], 
                                    'serial_number'=>$p["serial_number"],
                                    'itemName' => $p["itemName"],
                                    'costPrise' => $p["costPrice"],
                                    'cp_vat'=> $p["cp_vat"],
                                    'salePrise' => $p["salePrice"],
                                    'vat' => $p["vat"],
                                    'unit' => $newUnit,
                                    'stock' => 0,
                                    "created_at"  => Carbon::now(),
                                    "updated_at" => Carbon::now(),
                                ));

                                 if($p["qty"] > 0){

                                    $newOpenStock = OpeningStock::create(array(
                                        'open_stock_date' => Carbon::today(),
                                        'itemId' => $newItem, 
                                        'openingStock' => $p["qty"],
                                        'unit' => $newUnit,
                                        "created_at"  => Carbon::now(),
                                        "updated_at" => Carbon::now()
                                    ));


                                    $item = Item::find($newItem);

                                    $item->stock = $item->stock + $newOpenStock["openingStock"];
                                    $item->save();

                                    $openStock = Stock::create(array(
                                        "item_id" => $newItem, 
                                        "type" => 1, 
                                        "type_id" => $newOpenStock['id'], 
                                        "stock" => $item->stock, 
                                        "date"  =>$newOpenStock["open_stock_date"]                       
                                    ));
                                }
                            }

                        }

                    }


                }else{
                    $newItem = $item_check->first()->id;
                    $p["type"] ="duplicate";
                    array_push($duplicate, $p);
                }

            }else{

                if($p['vat'] < 0 || $p['vat'] >100 || $p["cp_vat"]< 0 || $p["cp_vat"] > 100){
                    $p["type"] ="vat";
                    array_push($duplicate, $p);
                }else{
                    $item = Item::find($p["id"]);

                    $unittrim=trim($p["unit"]);
                    $unit = Unit::where("unit", "like",$unittrim)->get();

                    if($unit->isEmpty()){
                        if($boolIsUnitCreate){
                            $newUnit = Unit::insertGetId(array(
                                "unit" => $p["unit"],
                                "created_at"  => Carbon::now(),
                                "updated_at" => Carbon::now()
                            ));
                        }else{
                            $newUnit = 0;
                        }

                    }else{
                        $newUnit = $unit->first()->id;
                    }

                    if(! is_null($item)){

                        if($boolIsItemUpdate){

                            if($p["qty"] > 0){

                                $newOpenStock = OpeningStock::create(array(
                                    'open_stock_date' => Carbon::today(),
                                    'itemId' => $item->id, 
                                    'openingStock' => $p["qty"],
                                    'unit' =>$newUnit,
                                    "created_at"  => Carbon::now(),
                                    "updated_at" => Carbon::now()
                                ));

                                $item->stock = $item->stock + $newOpenStock["openingStock"];
                                $item->serial_number=$p["serial_number"];
                                $item->costPrise=$p["costPrice"];
                                $item->salePrise=$p["salePrice"];
                                $item->vat=$p["vat"];
                                $item->cp_vat=$p["cp_vat"];
                                $item->save();

                                $stock = Stock::create(array(
                                    "item_id" => $item->id, 
                                    "type" => 1, 
                                    "type_id" => $newOpenStock['id'], 
                                    "stock" => $item->stock, 
                                    "date"  =>$newOpenStock["open_stock_date"]                       
                                ));

                            }else{
                                $item->serial_number=$p["serial_number"];
                                $item->costPrise=$p["costPrice"];
                                $item->salePrise=$p["salePrice"];
                                $item->vat=$p["vat"];
                                $item->cp_vat=$p["cp_vat"];
                                $item->save();

                            }




                        }

                    }
                }
            }
        }

        if(count($duplicate)==0){
            $res = array(
                "status"  => "success",
                "msg"   => trans("validation.success_store",[ "attribute" => trans('glob.item') ]),
                "arrIsCreate" => $arrIsCreate,
            );
        }else{
            $res = array(
                "status"  => "warning",
                "msg"   => trans("validation.success_store",[ "attribute" => trans('glob.item') ]),
                "duplicate" => $duplicate,
                "arrIsCreate" => $arrIsCreate,
            );
        }
    }else{
        $res = array(
            "status"  => "Error",
            "msg"   => trans("validation.error_store",[ "attribute" => trans('glob.item') ]),
            "duplicate" => $duplicate,
            "arrIsCreate" => $arrIsCreate,
        );
    }

    return $res;
    if($request->ajax()){

        return Response($res,200);

    }else{

        ($res["status"] == "success") 
        ? 
        toastr::success('Success', trans("validation.success_store",[ "attribute" => trans('glob.open_stock') ]))
        :
        toastr::error('error', trans("validation.error_store",[ "attribute" => trans('glob.open_stock') ]));
        return View("pages.master.opening_stock_entries.opening_stock",$res);
    }
}
catch(Exception $e){
    Log::error("OpeningStockController@saveOpenStock1 ".$e);
    return View("errors.exception");

}
}
}
