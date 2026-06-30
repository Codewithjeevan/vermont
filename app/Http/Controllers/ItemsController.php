<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Category;
use App\Item;
use App\Package;
use App\Unit;
use App\XYPromotion;
use Toastr;
use Common;
use Excel;
use PDF;
use Carbon\Carbon;
use App\OpeningStock;
use App\Stock;
use DB;
use Illuminate\Support\Facades\Input;
use App\PurchaseOrderItem;
use App\PurchasedItem;
use App\PurchaseReturnItem;
use App\BillingItems;
use App\BillReturnItems;
use App\IssueStockItem;
use App\DamageStockItem;
use App\ExpiryItems;
use App\Audit;
use Illuminate\Support\Facades\Auth;



class ItemsController extends Controller
{


    private $rDateFormat;
    private $rDateFormatSQL;
    private $intVatType;

    public function __construct()
    { 

        $this->rDateFormat = Common::rDateFormat();
        $this->rDateFormatSQL = Common::rDateFormatSQL($this->rDateFormat);
        $this->intVatType =Common::vatType();

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {    
        try{
            $arrReq = $request->all();

            $strFilter = $request->input('filter', '=') ;
            $dblVat = $request->input('vat','') ;

            ini_set('max_execution_time', 600);

            $boolIsCatCreate = Common::can_create('category');
            $boolIsUnitCreate = Common::can_create('unit');
            $boolIsOpenStockCreate = Common::can_create('open_stock');
            $boolIsItemUpdtae = Common::can_update('item');



            $items = $this->getItemData($request->input('searchkey', ''),$strFilter,$dblVat);


            $newId = Common::get_autoIncremenetVal('items');
            return View('pages.masters.items.item',[
                'searchkey' => $request->input('searchkey'),
                'items' => $items, 

                'newId'  => $newId ,
                'boolIsCatCreate'=>$boolIsCatCreate,
                'boolIsUnitCreate'=>$boolIsUnitCreate,
                'boolIsOpenStockCreate'=>$boolIsOpenStockCreate,
                'boolIsItemUpdtae'=>$boolIsItemUpdtae,
                'filter'   =>$strFilter,
                'vat'=> $dblVat,
            ]);
        }
        catch(Exception $e){
          Log::error("ItemsController@index ".$e);
          return View("errors.exception");
      }

  }

  public function getItems(){
    try{
        $items = Item::with("units")->get();
        $response["status"] = "success";
        $response["data"] =$items;
        return Response($response, 202);
    }
    catch(Exception $e){
      Log::error("ItemsController@getItems ".$e);
      return View("errors.exception");
  }

}

public function ajaxindex()
{   
    try{
        $categories = Category::all();
        $units = Unit::all();
        $boolIsCatCreate = Common::can_create('category');
        $boolIsUnitCreate = Common::can_create('unit');
        $boolIsOpenStockCreate = Common::can_create('open_stock');
        $boolIsItemUpdtae = Common::can_update('item');

        $newId = Common::get_autoIncremenetVal('items');

        return Response(['items' => [], 
            'categories' => $categories, 
            'newId'  => $newId,
            "units"=>$units,
            'boolIsCatCreate'=>$boolIsCatCreate,
            'boolIsUnitCreate'=>$boolIsUnitCreate,
            'boolIsOpenStockCreate'=>$boolIsOpenStockCreate,
            'boolIsItemUpdtae'=>$boolIsItemUpdtae,
        ]);
    }
    catch(Exception $e){
      Log::error("ItemsController@ajaxindex ".$e);
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
    {   try{
        $item = Item::create($request->all());
        toastr::success('Success', trans("validation.success_store",[ "attribute" => trans('glob.item') ]));
        return redirect()->action('ItemsController@index');
    }
    catch(Exception $e){
      Log::error("ItemsController@store ".$e);
      return View("errors.exception");
  }

}

public function ajaxstore(Request $request)
{

   try{
       $inputData = $request->all()["data"];

       $items = Item::with("categories")->get();
       $response = array();

       $response["focus"] = array();
       $duplicate = false;

       $barcode = Item::where("barcode","=",$inputData["barcode"])->first(); 
       if($barcode != null){
        $response["status"] ="warning";
        $response["message"] ="";
        $response["focus"] =[];
        array_push($response["focus"] ,"barcode");
        $response["data"] = $items;
        $duplicate = true;
    }
    

    if($inputData["productCode"]!=null || $inputData["productCode"]!=''  && count($response["focus"])==0){
        $productCode = Item::where("productCode","=",$inputData["productCode"])->first(); 
        if($productCode != null){
            $response["status"] ="warning";
            $response["message"] ="";
            $response["focus"] =[];
            array_push($response["focus"] ,"productCode");
            $response["data"] = $items;
            $duplicate = true;
        }
    }   


    

    if($duplicate){
        return Response($response, 200);
    }    

    $inputData["item_with_box"] = ( ($inputData["item_with_box"]=="true"|| $inputData["item_with_box"]== 1 ) ? 1 : 0 );
    $inputData["x_y_prmoChk"] = ( ($inputData["x_y_prmoChk"]=="true"|| $inputData["x_y_prmoChk"]== 1 ) ? 1 : 0 );
    $inputData["expiryChk"] = ( ($inputData["expiryChk"]=="true"|| $inputData["expiryChk"]== 1 ) ? 1 : 0 );

    $promoCheck = true;
    if (isset($inputData['xyPromo'])) {
        if(!(isset($inputData['xyPromo']['prmo_from'])) || !(isset($inputData['xyPromo']['prmo_to'])) || !(isset($inputData['xyPromo']['x_quantity'])) || !(isset($inputData['xyPromo']['y_quantity'])) || !(isset($inputData['xyPromo']['y_item_id']))){
            $promoCheck = false;
            $inputData["x_y_prmoChk"] = 0;
        }
    }else{
        $inputData["x_y_prmoChk"] = 0;
    }
    if(isset($inputData['packageList'])){

        $inputData["packageChk"] = 1;
    }else{
        $inputData["packageChk"] = 0;
    }

    if(($inputData["packageChk"]==true || $inputData["packageChk"]== 1) && isset($inputData["packageList"])){

        $inputData["packageChk"] = 1;
        $item = Item::create($inputData);

        if( $inputData["x_y_prmoChk"]== 1){

            if (isset($inputData['xyPromo']) && $promoCheck) {
                $xyPromoid = XYPromotion::create(array(
                    "x_item_id" => $item["id"],
                    "x_quantity" => $inputData["xyPromo"]["x_quantity"],
                    "y_item_id" => $inputData["xyPromo"]["y_item_id"],
                    "y_quantity" => $inputData["xyPromo"]["y_quantity"],
                    "prmo_from" => $inputData["xyPromo"]["prmo_from"],
                    "prmo_to" => $inputData["xyPromo"]["prmo_to"]
                ));
            }
        }

        for($i = 0; $i < sizeof($inputData["packageList"]); $i++){

            $pkgitem = Package::create(array(
                "itemId" => $item["id"],
                "packageId" => $inputData["packageList"][$i]["item"],
                "qty" => $inputData["packageList"][$i]["qty"]
            ));
        }

    }else{
        $inputData["packageChk"] = 0;
        $item = Item::create($inputData);

        if($inputData["x_y_prmoChk"]=='true' || $inputData["x_y_prmoChk"]== 1){

            if (isset($inputData['xyPromo']) && $promoCheck) {
                $xyPromoid = XYPromotion::create(array(
                    "x_item_id" => $item["id"],
                    "x_quantity" => $inputData["xyPromo"]["x_quantity"],
                    "y_item_id" => $inputData["xyPromo"]["y_item_id"],
                    "y_quantity" => $inputData["xyPromo"]["y_quantity"],
                    "prmo_from" => $inputData["xyPromo"]["prmo_from"],
                    "prmo_to" => $inputData["xyPromo"]["prmo_to"]
                ));
            }
        }
    }

    $response["status"] ="success";
    $response["message"] = trans("validation.success_store",["attribute" => trans('glob.item') ]);

    return Response($response, 200);
}
catch(Exception $e){
  Log::error("ItemsController@ajaxstore ".$e);
  return View("errors.exception");
}
}


public function ajaxedit($id)
{   
    try{
        $categories = Category::all();

        $editItem = Item::with('categories')->find($id);
        $packageList = [];

        foreach ($editItem->packageList as $package) {
            $item =Item::with('categories')->find($package["packageId"]);
            array_push($packageList,array(
                "category" => $item["category"],
                "item" => $package["packageId"],
                "itemName" => $item["itemName"],
                "item_name" => $item["itemName"],
                "barcode" => $item["barcode"],
                "pkgParentCategory" => $item["categories"]["parent_category"],
                "qty"   => $package["qty"]
            ));
        }

        $editItem["xyPromo"] = XYPromotion::with(['item','item.categories'])->where('x_item_id', '=', $id)->get();

        $editItem["packageList"] = $packageList;

        return Response(['editItem' => $editItem, 'items'=>[], 'categories' => $categories]);      
    }
    catch(Exception $e){
      Log::error("ItemsController@ajaxedit ".$e);
      return View("errors.exception");
  }  
}


public function ajaxupdate(Request $request, $id){


    /** check user auth */
    try{
        if (Auth::check())
            {
                $userId = \Auth::id();
            }

            $inputData = $request->all()["data"];

            $response["focus"] = array();
            $duplicate = false;
            $productCode = Item::where("productCode","=",$inputData["productCode"])->first(); 
            if($productCode!=null && $productCode["id"] != $id && $productCode["productCode"]!=null){
                $response["status"] ="Error";
                $response["message"] ="";
                array_push($response["focus"], "productCode");

                $duplicate = true;
            }

            $barcode = Item::where("barcode","=",$inputData["barcode"])->first(); 
            if($barcode != null && $barcode["id"] != $id){
                $response["status"] ="Error";
                $response["message"] ="";
                array_push($response["focus"], "barcode");

                $duplicate = true;
            }

            if($duplicate){
                return Response($response, 200);
            }

            $inputData["item_with_box"] = ( ($inputData["item_with_box"]=="true"|| $inputData["item_with_box"]== 1 ) ? 1 : 0 );

            $inputData["x_y_prmoChk"] = ( ($inputData["x_y_prmoChk"]=="true"|| $inputData["x_y_prmoChk"]== 1 ) ? 1 : 0 );
            $promoCheck = true;

            if (isset($inputData['xyPromo'])) {
                if(!(isset($inputData['xyPromo']['prmo_from'])) || !(isset($inputData['xyPromo']['prmo_to'])) || !(isset($inputData['xyPromo']['x_quantity'])) || !(isset($inputData['xyPromo']['y_quantity'])) || !(isset($inputData['xyPromo']['y_item_id']))){
                    $promoCheck = false;
                    $inputData["x_y_prmoChk"] = 0;
                }

            }else{

                $inputData["x_y_prmoChk"] = 0;
            }

            if(isset($inputData['packageList'])){
                $inputData["packageChk"] = 1;
            }else{
                $inputData["packageChk"] = 0;
            }

            $inputData["x_y_prmoChk"];

            $inputData["expiryChk"] = ( ($inputData["expiryChk"]=="true"|| $inputData["expiryChk"]== 1 ) ? 1 : 0 );


            \DB::table('packages')->where('itemId', '=', $inputData["id"])->delete();
            \DB::table('x_y_promotions')->where('x_item_id', '=', $inputData["id"])->delete();

            if($inputData["packageChk"]== 1){

                $inputData["packageChk"] = 1;
                $itemData = Item::findOrFail($id);
                $input = $inputData;
                $itemData->fill($input)->save();

                if($inputData["x_y_prmoChk"]=='true' || $inputData["x_y_prmoChk"]== 1){

                    if (isset($inputData['xyPromo']) && $promoCheck) {
                        $xyPromoid = XYPromotion::create(array(
                            "x_item_id" => $inputData["id"],
                            "x_quantity" => $inputData["xyPromo"]["x_quantity"],
                            "y_item_id" => $inputData["xyPromo"]["y_item_id"],
                            "y_quantity" => $inputData["xyPromo"]["y_quantity"],
                            "prmo_from" => $inputData["xyPromo"]["prmo_from"],
                            "prmo_to" => $inputData["xyPromo"]["prmo_to"]
                        ));
                    }
                }

                for($i = 0; $i < sizeof($inputData["packageList"]); $i++){

                    $pkgitem = Package::create(array(
                        "itemId" => $inputData["id"],
                        "packageId" => $inputData["packageList"][$i]["item"],
                        "qty" => $inputData["packageList"][$i]["qty"]
                    ));
                }

            }else{

                $inputData["packageChk"] = 0;
                $itemData = Item::findOrFail($id);
                $input = $inputData;
                $itemData->fill($input)->save();

                if($inputData["x_y_prmoChk"]=='true' || $inputData["x_y_prmoChk"]== 1){

                    if (isset($inputData['xyPromo']) && $promoCheck) {

                        $xyPromoid = XYPromotion::create(array(
                            "x_item_id" => $inputData["id"],
                            "x_quantity" => $inputData["xyPromo"]["x_quantity"],
                            "y_item_id" => $inputData["xyPromo"]["y_item_id"],
                            "y_quantity" => $inputData["xyPromo"]["y_quantity"],
                            "prmo_from" => $inputData["xyPromo"]["prmo_from"],
                            "prmo_to" => $inputData["xyPromo"]["prmo_to"]
                        ));
                    }
                }
            }

            /** save audit for the items with type id 1 @date->12-07-2017*/
            $auditSave = Audit::create(array(
                "record_id" => $id,
                "type" =>  1,
                "remark" =>  $inputData['remark'],
                "user_id" => $userId,
            ));

            $response["status"] ="success";
            $response["message"] = trans("validation.success_update",["attribute" => trans('glob.item') ]);

            return Response($response, 200);   
        }
        catch(Exception $e){
           Log::error("ItemsController@ajaxupdate ".$e);
           return View("errors.exception");

       } 
   }



   public function ajaxdelete($id)
   {
    try{
     $isOpeningStock = OpeningStock::where("itemId","=",$id)->first();
     $isPurchasedItems = PurchasedItem::where("item_id","=",$id)->first();
     $isPurchaseOrderItems = PurchaseOrderItem::where("item_id","=",$id)->first();
     $isPurchaseReturnItems = PurchaseReturnItem::where("item_id","=",$id)->first();
     $isExpiryItems = ExpiryItems::where("item_id","=",$id)->first();
     $isDamageStockItem = DamageStockItem::where("item_id","=",$id)->first();
     $isIssueStockItem = IssueStockItem::where("item_id","=",$id)->first();
     $isBillingItems = BillingItems::where("item_id","=",$id)->first();
     $isBillReturnItems = BillReturnItems::where("item_id","=",$id)->first();

     if(sizeof($isOpeningStock) == 0 && sizeof($isPurchasedItems) == 0 && sizeof($isPurchaseOrderItems) == 0 && sizeof($isPurchaseReturnItems) == 0 && sizeof($isExpiryItems) == 0 && sizeof($isDamageStockItem) == 0 && sizeof($isIssueStockItem) == 0 && sizeof($isBillingItems) == 0 && sizeof($isBillReturnItems) == 0){
        Item::find($id)->delete();
        Package::where('itemId', '=', $id)->delete();
        XYPromotion::where('x_item_id', '=', $id)->delete();
        $response["status"] ="success";
        $response["message"] =trans("validation.success_delete",["attribute" => trans('glob.item') ]);

    }else{
        $response = array('status' =>  "warning", "data" =>array(),  "message" =>' Item Stock Exists, Cant delete Item!' );
    }


    return Response($response,200);
}
catch(Exception $e){
   Log::error("ItemsController@ajaxdelete ".$e);
   return View("errors.exception");
}
}

     /**
     * uplode item profile function 
     */
     public function uploadItemProfile(Request $request){
        try{
            $i=1;

            $file = $request->file('file');
            $name = $file->getClientOriginalName();

            if(file_exists( public_path() . '/img/' .$name)) {
                $actual_name = pathinfo($name,PATHINFO_FILENAME);
                $original_name = $actual_name;
                $extension = pathinfo($name, PATHINFO_EXTENSION);
                $actual_name = (string)$original_name.$i;
                $name = $actual_name.".".$extension;
                $i++;
                $file->move(public_path().'/img', $name); 

                $response["status"] = "success";
                $response["imageUrl"] = $name;
                $response["message"] =  trans("validation.success_update",["attribute" => trans('glob.image') ]);
                return response($response, 200);
            } else {
                $file->move(public_path().'/img', $name); 

                $response["status"] = "success";
                $response["imageUrl"] = $name;
                $response["message"] =  trans("validation.success_update",["attribute" => trans('glob.image') ]);
                return response($response,200);
            } 
        }
        catch(Exception $e){
            Log::error("ItemsController@uploadItemProfile ".$e);
            return View("errors.exception");

        }
    }

    /** Get all item list for report */
    public function getItemList(Request $request){
        try{
            $arrReq = $request->all();

            $strFilter = (isset($arrReq['filter'])) ? $arrReq['filter'] : null ;
            $dblVat = (isset($arrReq['vat'])) ? $arrReq['vat'] : null ;
            $searchkey = (isset($arrReq['searchkey'])) ? $arrReq['searchkey'] : null ;

            $items = $this->getItemData($searchkey,$strFilter,$dblVat);

            return View('pages.reports.item_list.item_list',[
                'items'      => $items,
                'searchkey' => $searchkey,
                'filter'   =>$strFilter,
                'vat'=> $dblVat,
            ]);
        }
        catch(Exception $e){
            Log::error("ItemsController@getItemList ".$e);
            return View("errors.exception");

        }
    }

    /** Get all Expiry list for report  */
    public function getExpiryList(Request $request){   
       try{
        $expiry = [];

        $selected = $request->input('expiryDropdown', 0);

        $expiry = $this->getExpiryItemList($selected);

        if($request->ajax()){
            return Response([
                'expiry'      => $expiry,
                'expiryDropdown'      => $selected,
            ],200);
        }else{
            return View('pages.reports.expiry_list.expiry_list', [
                'expiry'      => $expiry,
                'expiryDropdown'     => $selected,
            ]);
        }
    }
    catch(Exception $e){
        Log::error("ItemsController@getExpiryList ".$e);
        return View("errors.exception");
    }

}

/** export to xls Item list */
public function exportToXlsItems(Request $request){
    try{
        $arrReq = $request->all();
        $strFilter = (isset($arrReq['filter'])) ? $arrReq['filter'] : null ;
        $dblVat = (isset($arrReq['vat'])) ? $arrReq['vat'] : null ;
        $searchkey = (isset($arrReq['searchkey'])) ? $arrReq['searchkey'] : null ;
        
        $item = $this->getExportItems($searchkey,$strFilter,$dblVat);

        if(sizeof($item)){
            \App\Legacy\LegacyExcel::create('Item', function ($excel) use($item){
               $carbon = Carbon::today();
               $timestamp = $carbon->timestamp;
               $format = $carbon->format('Y-m-d');
               $exportName = 'Item'.$format; 

               $excel->sheet($exportName, function ($sheet) use($item){


                $categories = Category::pluck('categorie', 'id');
                $units = Unit::pluck('unit', 'id');

                $itemList1 = array();
                if($this->intVatType!=0){
                    foreach ($item as $o) {    
                       if($o->serial_number == null){
                        $serial_number="NA";
                    }else{
                        $serial_number=$o->serial_number;
                    }          
                    $unit_ = (isset($o->units->unit)) ? $o->units->unit : '';
                    $date_ =  Carbon::parse($o->created_at); 
                    array_push($itemList1, array(
                     trans('glob.id')  => $o->id,
                     trans('item.item_product')  => $o->itemName,
                     trans('glob.barcode')  => $o->barcode,
                     trans('item.refrence_number')  => $serial_number,
                     trans('glob.category')  => $o->categories->parentCategory->categorie,
                     trans('glob.sub_category')  => $o->categories->categorie,
                     trans('item.c_p')  =>   (isset($o->costPrise)) ? " ".(\Common::fixIt($o->costPrise)) : "0" ,
                     trans('item.cp_vat_percent')  =>   (isset($o->cp_vat)) ? " ".(\Common::fixIt($o->cp_vat)) : "0",
                     trans('item.s_p')  =>   (isset($o->salePrise)) ? " ".(\Common::fixIt($o->salePrise)) : "0" ,
                     trans('glob.sp_vat')  =>   (isset($o->vat)) ? " ".(\Common::fixIt($o->vat)) : "0",                                      
                 )
                );
                }
            }
            else{
                foreach ($item as $o) {              
                    $unit_ = (isset($o->units->unit)) ? $o->units->unit : '';
                    $date_ =  Carbon::parse($o->created_at); 
                    array_push($itemList1, array(
                     trans('glob.id')  => $o->id,
                     trans('item.item_product')  => $o->itemName,
                     trans('glob.barcode')  => $o->barcode,
                     trans('item.refrence_number')  =>  $serial_number,
                     trans('glob.category')  =>  $o->categories->parentCategory->categorie,
                     trans('glob.sub_category')  => $o->categories->categorie,
                     trans('item.c_p')  =>   (isset($o->costPrise)) ? " ".(\Common::fixIt($o->costPrise)) : "0" ,
                     trans('item.s_p')  =>   (isset($o->salePrise)) ? " ".(\Common::fixIt($o->salePrise)) : "0",                                    
                 )
                );
                }              

            }              

            /* setting column names for data - you can of course set it manually */
            $sheet->appendRow(array_keys($itemList1[0])); /* column names */

            /* getting last row number (the one we already filled and setting it to bold */
                $sheet->row($sheet->getHighestRow(), function ($row) {
                    $row->setFontWeight('bold');
                });

                /* putting users data as next rows */
                foreach ($itemList1 as $user) {
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
        Log::error("ItemsController@exportToXlsItems ".$e);
        return View("errors.exception");

    }

}


/** export to PDF Item list */
public function exportToPdfItems(Request $request){
    try{
      $arrReq = $request->all();
      $strFilter = (isset($arrReq['filter'])) ? $arrReq['filter'] : null ;
      $dblVat = (isset($arrReq['vat'])) ? $arrReq['vat'] : null ;
      $searchkey = (isset($arrReq['searchkey'])) ? $arrReq['searchkey'] : null ;

      $item = $this->getExportItems($searchkey,$strFilter,$dblVat);

      if(sizeof($item)){
        $pdf = PDF::loadView('pages.reports.item_list.item_pdf', [
            'item' => $item
        ]); 

        $pdf->setPaper('a4');
        $carbon = Carbon::today();
        $timestamp = $carbon->timestamp;
        $format = $carbon->format('Y-m-d');
        $exportName = 'Item'.$format.'.pdf'; 

        return $pdf->download($exportName);

    }else{
        toastr::warning('Warning', trans("validation.export_error"));
        return back();
    }
}
catch(Exception $e){
    Log::error("ItemsController@exportToPdfItems ".$e);
    return View("errors.exception");

}
}

/** export to xls Expiry list */
public function exportToExpiry(Request $request){
    try{
      $dateArray = $request->all(); 
      $selected = $dateArray['expiryDropdown'];

      $expiry = $this->getExpiryItemList($selected);


      if(Input::get('pdf')){
       $pdf = PDF::loadView('pages.reports.expiry_list.expiry_pdf', [
        'expiry' => $expiry,'i' => 1,
    ]); 

       $pdf->setPaper('a4');
       $carbon = Carbon::today();
       $timestamp = $carbon->timestamp;
       $format = $carbon->format($this->rDateFormat);
       $exportName = 'ExpiryItem'.$format.'.pdf';
       return $pdf->download($exportName);
   }else if(Input::get('xls')){
       if(sizeof($expiry) >0){
           $carbon = Carbon::today();
           $timestamp = $carbon->timestamp;
           $format = $carbon->format($this->rDateFormat);
           $exportName = 'ExpiryItem'.$format; 
           \App\Legacy\LegacyExcel::create($exportName, function ($excel) use($expiry){

            $excel->sheet('ExpiryItem', function ($sheet) use($expiry){


                $sheet->appendRow(array(
                 '#' ,
                 trans('item.item_name') ,
                 trans('stock.mrn_no')  ,                                   
                 trans('glob.expiry_date') ,                      

             )); 
                $sheet->row($sheet->getHighestRow(), function ($row) {
                    $row->setFontWeight('bold');
                });

                $i=1;
                foreach ($expiry as $o) {              

                    $sheet->appendRow( array(
                     '#'  => $i++,
                     trans('item.item_name')  => $o->itemName,
                     trans('stock.mrn_no')  => $o->purchase_id,                                   
                     trans('glob.expiry_date')  => $o->expiryDate,                      

                 )
                );
                }                           
            });

        })->export('xlsx');


           return back();

       }else{
        toastr::warning('Warning', trans("validation.export_error"));
        return back();
    }
}
}
catch(Exception $e){
    Log::error("ItemsController@exportToExpiry ".$e);
    return View("errors.exception");

}

}
/** export to PDF Expiry list */
public function exportToPdfExpiry(){
    try{
     $expiry = [];
     $selected = $request->all()["data"]; 
     $expiry = $this->getExpiryItemList($selected);

     if(sizeof($expiry)){
        $pdf = PDF::loadView('pages.reports.expiry_list.expiry_pdf', [
            'expiry' => $expiry
        ]); 

        $pdf->setPaper('a4');
        $carbon = Carbon::today();
        $timestamp = $carbon->timestamp;
        $format = $carbon->format('Y-m-d');
        $exportName = 'ExpiryItem'.$format.'.pdf';
        return $pdf->download($exportName);

    }else{
        toastr::warning('Warning', trans("validation.export_error"));
        return back();
    }
}
catch(Exception $e){
    Log::error("ItemsController@exportToPdfExpiry ".$e);
    return View("errors.exception");

}

}

public function uploadFile(Request $request){
    try{
        ini_set('max_execution_time', 600);
        ini_set('upload_max_filesize', '15M');
        $i=0;
        $file = $request->file('item_import');
        $name = $file->getClientOriginalName();
        $items = array();

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


        $excelData = \App\Legacy\LegacyExcel::load(storage_path().'/import/'. $name, function($reader) {})->get();


        foreach ($excelData as $e) {

            $check = false;
            if($e->item_code!=''){
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
                    ->get();

                    if($conflict_check->isEmpty()){
                        $check = true;
                    }else{
                        $check = false;
                    }

                    $extItem = array(
                        "id" => $it_first->id, 
                        'parent_category'=>$e->category,
                        'category'  => $e->sub_category ,
                        'barcode'  => $e->item_code,
                        'serial_number'=>$e->reference_number,
                        'itemName'  =>  $e->item_name,
                        'unit'      =>  $e->unit,
                        'costPrice'  =>  $e->cost_price,
                        'cp_vat'  => ($this->intVatType!=0)?$e->cp_vat:0,  
                        'salePrice'  => $e->selling_price, 
                        'vat'  => $e->vat,  
                        'quantity'  =>  $e->qty,
                        'type'  => "old",
                        'conflict'  => $check,
                    );
                    array_push($items, $extItem);

                }else{

                    $newItem = array(
                        "id" => 0, 
                        'parent_category'=>$e->category,
                        'category'  =>  $e->sub_category,

                        'barcode'  =>  $e->item_code,
                        'serial_number'=>$e->reference_number,
                        'itemName'  =>  $e->item_name,
                        'unit'      => $e->unit,
                        'costPrice'  =>  $e->cost_price,
                        'cp_vat'  => ($this->intVatType!=0)?$e->cp_vat:0,  
                        'salePrice'  => $e->selling_price,
                        'vat'  => ($this->intVatType!=0)?$e->vat:0,  
                        'quantity'  =>  $e->qty,
                        'type'  => "new",
                        'conflict'  => $check,
                    );
                    array_push($items, $newItem);
                }
            }
        }

        $response["status"] = "success";
        $response["items"] = $items;
        return response($response, 200);
    }
    catch(Exception $e){
        Log::error("ItemsController@exportToPdfExpiry ".$e);
        return View("errors.exception");

    }

}

function saveitems(Request $request){
    try{
        $importedItems = $request->all()["data"];
        $duplicate = array();


        $boolIsCatCreate = Common::can_create('category');

        $boolIsUnitCreate = Common::can_create('unit');

        $boolIsItemUpdate = Common::can_update('item');

        $boolIsOpeningStockCreate = Common::can_create('open_stock');

        if(!is_null($importedItems)){

            foreach ($importedItems as $p) { 

                $p = (Array)$p;
                $p["parent_category"]=(isset($p["parent_category"])) ? $p["parent_category"] : '';
                $p["category"] = (isset($p["category"])) ? $p["category"] : '';
                $p["unit"] = (isset($p["unit"])) ? $p["unit"] : 'pc';
                $p["barcode"] = (isset($p["barcode"])) ? $p["barcode"] : '';
                $p["serial_number"] = (isset($p["serial_number"])) ? $p["serial_number"] : '';
                $p["itemName"] = (isset($p["itemName"])) ? $p["itemName"] : '';
                $p["salePrice"] = (isset($p["salePrice"])) ? $p["salePrice"] : 0;
                $p["costPrice"] = (isset($p["costPrice"])) ? $p["costPrice"] : 0;
                $p["quantity"] = (isset($p["quantity"])) ? $p["quantity"] : 0;
                $p["vat"] = (isset($p["vat"])) ? $p["vat"] : 0;
                $p["cp_vat"] = (isset($p["cp_vat"])) ? $p["cp_vat"] : 0;
                if($p["vat"] < 0 || $p["vat"]>100){

                    $res = array(
                        "status"  => "Error",
                        "msg"   => trans("validation.export_vat",[ "attribute" => trans('glob.item') ]),
                    );

                    if($request->ajax()){
                        return Response($res,200);
                    }else{
                        ($res["status"] == "success") 
                        ? 
                        toastr::success('Success', trans("validation.success_store",[ "attribute" => trans('glob.purchase') ]))
                        :
                        toastr::error('error', trans("validation.import_error",[ "attribute" => trans('glob.purchase') ]));
                        return View("pages.master.item.item",$res);
                    }
                }

                if($p["cp_vat"] < 0 || $p["cp_vat"]>100){

                    $res = array(
                        "status"  => "Error",
                        "msg"   => trans("validation.import_vat",[ "attribute" => trans('glob.item') ]),
                    );

                    if($request->ajax()){
                        return Response($res,200);
                    }else{
                        ($res["status"] == "success") 
                        ? 
                        toastr::success('Success', trans("validation.success_store",[ "attribute" => trans('glob.purchase') ]))
                        :
                        toastr::error('error', trans("validation.import_error",[ "attribute" => trans('glob.purchase') ]));
                        return View("pages.master.item.item",$res);
                    }
                }
                if($p["id"]==0){
                   
                    if(!empty($p["parent_category"])){
                       $parentcat=Category::where("categorie","like",$p["parent_category"])->get();
                   
                      if($parentcat->isEmpty()){


                            if($boolIsCatCreate){

                                $newParentCat = Category::insertGetId(array(
                                    "categorie" => $p["parent_category"],
                                    "parent_category" => 0,
                                    "created_at"  => Carbon::now(),
                                    "updated_at" => Carbon::now()
                                ));
                            }else{
                                $newParentCat = 0;

                            }

                        }else{
                            $newParentCat = $parentcat->first()->id;
                        }

                    }
                    else{
                          $newParentCat=0;
                    }
                    if(!empty($p["category"]) && !empty($p["parent_category"])){
                        $cat = Category::where("parent_category","<>", 0)
                        ->where("categorie", "like", $p["category"])
                        ->get();

                        if($cat->isEmpty()){


                            if($boolIsCatCreate){

                                $newCat = Category::insertGetId(array(
                                    "categorie" => $p["category"],
                                    "parent_category" => $newParentCat,
                                    "created_at"  => Carbon::now(),
                                    "updated_at" => Carbon::now()
                                ));
                            }else{
                                $newCat = 0;

                            }

                        }else{
                            $newCat = $cat->first()->id;
                        }
                    }
                    else{
                        if(!empty($p["category"]) && empty($p["parent_category"]))
                         {
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
                                $newCat = 0;

                            }

                        }else{
                            $newCat = $cat->first()->id;
                        }
                         }
                         else{
                             $newCat = 0;
                         }
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
                            $newUnit =0;

                        }


                    }else{
                        $newUnit = $unit->first()->id;
                    }


                    $item_check = Item::where("barcode","=", $p["barcode"])->get();
                    if($item_check->isEmpty()){

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

                        if($newCat!=0 && $newUnit!=0){



                            if($boolIsOpeningStockCreate){

                                $newItem = Item::create(array(
                                    'category' => $newCat,
                                    'barcode' => $p["barcode"], 
                                    'serial_number'=>$p["serial_number"],
                                    'itemName' => $p["itemName"],
                                    'costPrise' => $p["costPrice"],
                                    'cp_vat' => $p["cp_vat"],
                                    'salePrise' => $p["salePrice"],
                                    'vat' => $p["vat"],
                                    'unit' => $newUnit,
                                    'stock' => $p["quantity"],
                                ));
                                if($p["quantity"] >0){
                                    $newOpenStock = OpeningStock::create(array(
                                        'open_stock_date' => Carbon::today(),
                                        'itemId' => $newItem['id'], 
                                        'openingStock' => $p["quantity"],
                                        'unit' => $newUnit,
                                        "created_at"  => Carbon::now(),
                                        "updated_at" => Carbon::now()
                                    ));

                                    $openStock = Stock::create(array(
                                        "item_id" => $newItem['id'], 
                                        "type" => 1, 
                                        "type_id" => $newOpenStock['id'], 
                                        "stock" => $p["quantity"], 
                                        "date"  =>$newOpenStock["open_stock_date"]                       
                                    ));
                                }
                            }else{
                                $newItem = Item::create(array(
                                    'category' => $newCat,
                                    'barcode' => $p["barcode"],
                                    'serial_number'=>$p["serial_number"],
                                    'itemName' => $p["itemName"],
                                    'costPrise' => $p["costPrice"],
                                    'cp_vat' => $p["cp_vat"],
                                    'salePrise' => $p["salePrice"],
                                    'vat' => $p["vat"],
                                    'unit' => $newUnit,
                                    'stock' => 0,
                                ));
                            }

                        }
                    }else{
                        $p["type"] ="duplicate";
                        array_push($duplicate, $p);
                    }

                }else{
                    $item = Item::find($p["id"]);
                    if( trim($p["serial_number"])==''){
                        $p["type"] ="duplicate";
                        array_push($duplicate, $p);
                        continue;
                    }
                    if(! is_null($item)){

                       if($boolIsItemUpdate){
                        if($boolIsOpeningStockCreate){

                            $item->stock = $item->stock + $p["quantity"];
                            $item->serial_number=$p["serial_number"];
                            $item->costPrise = $p["costPrice"];
                            $item->cp_vat = $p["cp_vat"];
                            $item->salePrise = $p["salePrice"];
                            $item->vat = $p["vat"];
                            $item->save();
                            if($p["quantity"]>0){
                                $newOpenStock = OpeningStock::create(array(
                                    'open_stock_date' => Carbon::today(),
                                    'itemId' => $item->id, 
                                    'openingStock' => $p["quantity"],
                                    'unit' => $item->unit,
                                    "created_at"  => Carbon::now(),
                                    "updated_at" => Carbon::now()
                                ));

                                $stock = Stock::create(array(
                                    "item_id" => $item->id, 
                                    "type" => 1, 
                                    "type_id" => $newOpenStock['id'], 
                                    "stock" => $item->stock, 
                                    "date"  =>$newOpenStock["open_stock_date"]                       
                                ));
                            }
                        }else{

                            $item->stock = $item->stock;
                            $item->costPrise = $p["costPrice"];
                            $item->serial_number=$p["serial_number"];
                            $item->cp_vat=$p["cp_vat"];
                            $item->salePrise = $p["salePrice"];
                            $item->vat = $p["vat"];
                            $item->save();

                        }
                    }

                }
            }
        }

        if(count($duplicate)==0){
            $res = array(
                "status"  => "success",
                "msg"   => trans("validation.success_store",[ "attribute" => trans('glob.item') ]),
            );
        }else{
            $res = array(
                "status"  => "warning",
                "msg"   => trans("validation.success_store",[ "attribute" => trans('glob.item') ]),
                "duplicate" => $duplicate
            );
        }
    }else{
        $res = array(
            "status"  => "Error",
            "msg"   => trans("validation.error_store",[ "attribute" => trans('glob.item') ]),
            "duplicate" => $duplicate
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
    Log::error("ItemsController@saveitems ".$e);
    return View("errors.exception");

}
}


function getSingleItemByName(Request $request){
 try{
    $data = $request->all()['data'];

    if($data['category']==null || $data['category']==''){
        $item = Item::with(["units","categories"])
        ->where('itemName','=', $data['itemName'])->first(); 
    }else{
        $item = Item::with(["units","categories"])
        ->where('itemName','=', $data['itemName'])
        ->where('category','=', $data['category'])->first(); 
    }


    return Response(array(
       'item' => $item,
   ),200);
}
catch(Exception $e){
    Log::error("ItemsController@getSingleItemByName ".$e);
    return View("errors.exception");

}
}

function getSingleItemByBarcode(Request $request){
    try{

        $data = $request->all()['data'];

        if($data['category']==null || $data['category']==''){
            $item = Item::with(["units","categories"])
            ->where('barcode','=', $data['barcode'])->first(); 
        }else{
         $item = Item::with(["units","categories"])
         ->where('barcode','=', $data['barcode'])
         ->where('category','=', $data['category'])->first(); 
     }


     return Response(array(
       'item' => $item,
   ),200);
 }
 catch(Exception $e){
    Log::error("ItemsController@getSingleItemByBarcode ".$e);
    return View("errors.exception");

}

}

function getpkgItemByName(Request $request){
    try{
        $data = $request->all()['data'];

        if($data['category']==null || $data['category']==''){
            $item = Item::with(["units","categories"])
            ->where('itemName','=', $data['itemName'])->first(); 
        }else{
            $item = Item::with(["units","categories"])
            ->where('itemName','=', $data['itemName'])
            ->where('category','=', $data['category'])->first(); 
        }


        return Response(array(
           'item' => $item,
       ),200);
    }
    catch(Exception $e){
        Log::error("ItemsController@getpkgItemByName ".$e);
        return View("errors.exception");

    }


}

function getpkgItemByBarcode(Request $request){
    try{
        $data = $request->all()['data'];

        if($data['category']==null || $data['category']==''){
            $item = Item::with(["units","categories"])
            ->where('barcode','=', $data['barcode'])->first(); 
        }else{
         $item = Item::with(["units","categories"])
         ->where('barcode','=', $data['barcode'])
         ->where('category','=', $data['category'])->first(); 
     }


     return Response(array(
       'item' => $item,
   ),200);
 }
 catch(Exception $e){
    Log::error("ItemsController@getpkgItemByBarcode ".$e);
    return View("errors.exception");

}

}

function searchItem(Request $request){
    try{

        $intId = 0;
        $strItemName = '';
        $strBarcode = '';
        $intCategory = 0 ;
        $productCode ='';
        $serial_number='';

        $objData = $request->all()['data'];

        $intId = (isset($objData['id'])) ?  $objData['id'] : $intId;
        $strItemName = (isset($objData['itemName'])) ?  addslashes($objData['itemName']) : $strItemName;
        $strBarcode = (isset($objData['barcode'])) ?  $objData['barcode'] : $strBarcode;
        $intCategory = (isset($objData['category']) && $objData['category'] != '') ?  $objData['category'] : $intCategory;
        $productCode = (isset($objData['productCode']) && $objData['productCode'] != '') ?  $objData['productCode'] : $productCode;
        $serial_number=(isset($objData['serial_number'])) ?  $objData['serial_number'] : $serial_number;



        if($strBarcode =='' && $strItemName == ''){
            $item = null;
        }else{
            $item = Item::with(["categories", "units"])
            ->whereRaw("(CASE WHEN ? = 0 THEN 1 ELSE items.id = ? END) AND 
                (CASE WHEN ? LIKE '' THEN 1 ELSE items.productCode LIKE ? END) AND
                (CASE WHEN ? LIKE '' THEN 1 ELSE items.itemName LIKE ? END) AND
                (CASE WHEN ? LIKE '' THEN 1 ELSE items.barcode LIKE ? END) AND
                (CASE WHEN ? LIKE '' THEN 1 ELSE items.serial_number LIKE ? END) AND
                (CASE WHEN ? = 0 THEN 1 ELSE items.category = ? END) 
                ", [
                    $intId, $intId,
                    $productCode, $productCode,
                    $strItemName, $strItemName,
                    $strBarcode, $strBarcode,
                    $serial_number, $serial_number,
                    $intCategory, $intCategory,
                ])->first();
        }

        return Response(array(
           'item' => $item,
       ),200);
    }
    catch(Exception $e){
        Log::error("ItemsController@searchItem ".$e);
        return View("errors.exception");

    }

}

function searchBarcode(Request $request){
    try{
        $objData =$request->all();
        $intId = 0;
        $strItemName = '';
        $strBarcode = '';
        $intCategory = 0 ;
        $serial_number='';


        $intId = (isset($objData['id'])) ?  $objData['id'] : $intId;
        $strItemName = (isset($objData['itemname'])) ? addslashes($objData['itemname']) : $strItemName;
        $strBarcode = (isset($objData['term'])) ?  $objData['term'] : $strBarcode;
        $intCategory = (isset($objData['cat']) && $objData['cat'] != '') ?  $objData['cat'] : $intCategory;
        $serial_number=(isset($objData['serial_number'])) ?  $objData['serial_number'] : $serial_number;

        $resultdb = Item::select("barcode")
        ->whereRaw("(CASE WHEN ? = 0 THEN 1 ELSE items.id = ? END) AND 
            (CASE WHEN ? LIKE '' THEN 1 ELSE items.itemName LIKE ? END) AND
            (CASE WHEN ? LIKE '' THEN 1 ELSE items.barcode LIKE ? END) AND
            (CASE WHEN ? LIKE '' THEN 1 ELSE items.serial_number LIKE ? END) AND
            (CASE WHEN ? = 0 THEN 1 ELSE items.category = ? END) 
            ", [
                $intId, $intId,
                $strItemName, '%'.$strItemName.'%',
                $strBarcode, '%'.$strBarcode.'%',
                $serial_number, '%'.$serial_number.'%',
                $intCategory, $intCategory,
            ])->take(10)->get()->toArray();

        $barcodes = array_column($resultdb, 'barcode');


        return Response($barcodes);
    }
    catch(Exception $e){
        Log::error("ItemsController@searchBarcode ".$e);
        return View("errors.exception");

    }
}


function searchItemName(Request $request){
    try{
        $objData =$request->all();
        $intId = 0;
        $strItemName = '';
        $strBarcode = '';
        $intCategory = 0 ;

        $intId = (isset($objData['id'])) ?  $objData['id'] : $intId;
        $strItemName = (isset($objData['term'])) ? addslashes($objData['term']) : $strItemName;
        $strBarcode = (isset($objData['barcode'])) ?  $objData['barcode'] : $strBarcode;
        $intCategory = (isset($objData['cat']) && $objData['cat'] != '') ?  $objData['cat'] : $intCategory;

        $resultdb = Item::select("itemName")
        ->whereRaw("(CASE WHEN $intId = 0 THEN 1 ELSE items.id = $intId END) AND 
            (CASE WHEN '$strItemName' LIKE '' THEN 1 ELSE items.itemName LIKE '%$strItemName%' END) AND
            (CASE WHEN '$strBarcode' LIKE '' THEN 1 ELSE items.barcode LIKE '%$strBarcode%' END) AND
            (CASE WHEN $intCategory = 0 THEN 1 ELSE items.category = $intCategory END) 
            ")->take(10)->get()->toArray();

        $barcodes = array_column($resultdb, 'itemName');


        return Response($barcodes);
    }
    catch(Exception $e){
        Log::error("ItemsController@searchItemName ".$e);
        return View("errors.exception");

    }
}

    /**
     * anniversary list for reports
     * @return  \report\anniversary_list
     */
    public function getExpiryListAll(Request $request){
        try{
           $expiry = [];
           $selected = $request->all()["data"]; 

           $expiry = $this->getExpiryItemList($selected);


           $response["expiry"] = $expiry;

           return Response($response, 200); 
       }
       catch(Exception $e){
        Log::error("ItemsController@getExpiryListAll ".$e);
        return View("errors.exception");

    }

}

/** export to PDF Item list for item Master */
public function exportToPdfItemsMaster(Request $request){
    try{

        $arrReq = $request->all();
        $strFilter = (isset($arrReq['filter'])) ? $arrReq['filter'] : '=' ;
        $dblVat = (isset($arrReq['vat'])) ? $arrReq['vat'] : null ;
        $searchkey = (isset($arrReq['searchkey'])) ? $arrReq['searchkey'] : null ;

        $item = $this->getExportItems($searchkey,$strFilter,$dblVat);

        if(sizeof($item)){
            $pdf = PDF::loadView('pages.masters.items.item_pdf', [
                'item' => $item,
                'i'=>1,
            ]); 

            $pdf->setPaper('a4');
            $carbon = Carbon::today();
            $timestamp = $carbon->timestamp;
            $format = $carbon->format('Y-m-d');
            $exportName = 'Item'.$format.'.pdf'; 

            return $pdf->download($exportName);

        }else{
            toastr::warning('Warning', trans("validation.export_error"));
            return back();
        }
    }
    catch(Exception $e){
        Log::error("ItemsController@exportToPdfItemsMaster ".$e);
        return View("errors.exception");

    }

}

/** export to xls Item list for item Master */
public function exportToXlsItemsMaster(Request $request){
    try{
       $arrReq = $request->all();
       $strFilter = (isset($arrReq['filter'])) ? $arrReq['filter'] : null ;
       $dblVat = (isset($arrReq['vat'])) ? $arrReq['vat'] : null ;
       $searchkey = (isset($arrReq['searchkey'])) ? $arrReq['searchkey'] : null ;

       $item = $this->getExportItems($searchkey,$strFilter,$dblVat);

       if(sizeof($item)){
        \App\Legacy\LegacyExcel::create('Item', function ($excel) use($item){
           $carbon = Carbon::today();
           $timestamp = $carbon->timestamp;
           $format = $carbon->format('Y-m-d');
           $exportName = 'Item'.$format; 

           $excel->sheet($exportName, function ($sheet) use($item){
            $i=1;
            $itemList1 = array();
            if($this->intVatType!=0){
                foreach ($item as $o) {              
                    $unit_ = (isset($o->units->unit)) ? $o->units->unit : '';
                    $date_ =  Carbon::parse($o->created_at); 
                       if($o->serial_number == null){
                        $serial_number="NA";
                    }else{
                        $serial_number=$o->serial_number;
                    } 
                    array_push($itemList1, array(
                     'Sr.No'  => $i++,
                     'Item Name' =>  $o->itemName,
                     'Item Code' =>  $o->barcode,
                     'Reference Number'=>$serial_number,
                     'Category'=> $o->categories->parentCategory->categorie,
                     'Sub-Category'  =>  $o->categories->categorie,
                     'Unit'  =>  $unit_,
                     'Qty'   =>  " ".\Common::fixQty($o->stock),
                     'Selling Price' =>  (isset($o->salePrise)) ? " ".(\Common::fixIt($o->salePrise)) : "0" ,
                     'VAT'   => (isset($o->vat)) ? " ".(\Common::fixIt($o->vat)) : "0" ,
                     'Cost Price'    => (isset($o->costPrise)) ? " ".(\Common::fixIt($o->costPrise)) : "0" ,
                     'CP VAT'    => (isset($o->cp_vat)) ? " ".(\Common::fixIt($o->cp_vat)) : "0" ,
                     trans('glob.min')  =>   " ".\Common::fixQty($o->min), 
                     trans('glob.max')  =>   " ".\Common::fixQty($o->max),                         
                     trans('glob.product')  => $o->productCode,   
                     trans('glob.description') => $o->description,              
                     trans('glob.created_date') => $date_->format('F d, Y')                       
                 )
                );
                } 
            }
            else{
                foreach ($item as $o) {              
                    $unit_ = (isset($o->units->unit)) ? $o->units->unit : '';
                    $date_ =  Carbon::parse($o->created_at); 
                    array_push($itemList1, array(
                     'Sr.No'  => $i++,
                     'Item Name' =>  $o->itemName,
                     'Item Code' =>  $o->barcode,
                     'Refrence Number'=>$o->serial_number,
                     'Category'=> $o->categories->parentCategory->categorie,
                     'Sub-Category'  =>  $o->categories->categorie,
                     'Unit'  =>  $unit_,
                     'Qty'   =>  " ".\Common::fixQty($o->stock),
                     'Selling Price' =>  (isset($o->salePrise)) ? " ".(\Common::fixIt($o->salePrise)) : "0" ,
                     'Cost Price'    => (isset($o->costPrise)) ? " ".(\Common::fixIt($o->costPrise)) : "0" ,



                     trans('glob.min')  =>   " ".\Common::fixQty($o->min), 
                     trans('glob.max')  =>   " ".\Common::fixQty($o->max),                         
                     trans('glob.product')  => $o->productCode,   
                     trans('glob.description') => $o->description,              
                     trans('glob.created_date') => $date_->format('F d, Y')                       
                 )
                );
                }           
            }             

            /* setting column names for data - you can of course set it manually */
            $sheet->appendRow(array_keys($itemList1[0])); /* column names */

            /* getting last row number (the one we already filled and setting it to bold */
                $sheet->row($sheet->getHighestRow(), function ($row) {
                    $row->setFontWeight('bold');
                });

                /* putting users data as next rows */
                foreach ($itemList1 as $user) {
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
    Log::error("ItemsController@exportToXlsItemsMaster ".$e);
    return View("errors.exception");

}
}

public function getExportItems($searchkey,$strFilter,$dblVat){
    try{

        if($searchkey !=null ){

            return  Item::with(["units","categories.parentCategory"])->join('categories', 'items.category', '=', 'categories.id')
            ->select('items.*', 'categories.categorie')
            ->orWhere('categories.categorie','LIKE','%'.$searchkey.'%')
            ->orWhere('barcode','LIKE','%'.$searchkey.'%')
            ->orWhere('itemName','LIKE','%'.$searchkey.'%')
            ->orderBy('id', 'asc')
            ->get();

        }else{
            if($strFilter!=null && $dblVat!=null){
              return Item::with(["units","categories","categories.parentCategory"])
              ->orwhere('vat',$strFilter,$dblVat)
              ->orwhere('cp_vat',$strFilter,$dblVat)->get();
          }
          else{
           return Item::with(["units","categories","categories.parentCategory"])->get();
       }
   }
}
catch(Exception $e){
    Log::error("ItemsController@getExportItems ".$e);
    return View("errors.exception");
}
}

public function getItemData($searchkey,$strFilter,$dblVat){
    try{
        if($searchkey !=null ){
         return Item::with(["units","categories.parentCategory"])->join('categories', 'items.category', '=', 'categories.id')
         ->select('items.*', 'categories.categorie')
         ->orWhere('categories.categorie','LIKE','%'.$searchkey.'%')
         ->orWhere('barcode','LIKE','%'.$searchkey.'%')
         ->orWhere('itemName','LIKE','%'.$searchkey.'%')
          ->orWhere('serial_number','LIKE','%'.$searchkey.'%')
         ->orderBy('id', 'asc')
         ->paginate(25);
     }else{
        if($strFilter!=null && $dblVat!=null){
          return Item::with(["units","categories","categories.parentCategory"])->orwhere('vat',$strFilter,$dblVat)->orwhere('cp_vat',$strFilter,$dblVat)->paginate(25);
      }
      else{
       return Item::with(["units","categories","categories.parentCategory"])->paginate(25);
   }
}
}
catch(Exception $e){
    Log::error("ItemsController@getItemData ".$e);
    return View("errors.exception");

}
}

public function getExpiryItemList($selected){
    try{

        return DB::select("SELECT p.id,p.purchase_id,
            DATE_FORMAT(p.expiryDate, '".$this->rDateFormatSQL."') as expiryDate ,
            i.itemName 
            FROM purchased_items p INNER JOIN items i on p.item_id=i.id 
            WHERE CASE WHEN ($selected = 0)  THEN MONTH(expiryDate) = MONTH(CURRENT_DATE()) and YEAR(expiryDate) = YEAR(CURRENT_DATE())
            ELSE p.expiryDate <> 'null' END
            union
            SELECT i.id as id ,'Initial Stock' as purchase_id,
            DATE_FORMAT(i.expiry_date , '".$this->rDateFormatSQL."') as expiryDate,
            i.itemName 
            FROM items i 
            WHERE CASE WHEN ($selected = 0)  THEN MONTH(expiry_date) = MONTH(CURRENT_DATE()) and YEAR(expiry_date) = YEAR(CURRENT_DATE())
            ELSE i.expiry_date <> 'null' END
            ");
    }

    catch(Exception $e){
        Log::error("ItemsController@getExpiryItemList ".$e);
        return View("errors.exception");

    }

}
}
