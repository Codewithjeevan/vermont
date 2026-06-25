<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
use App\Category;
use App\ClubBarcode;

class ClubBarcodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        try{
            $categories = Category::all();
            $newId = \Common::get_autoIncremenetVal("club_barcodes");
        //$clubItems = ClubBarcode::with('items')->get();
            $res = array(
                
                'categories'    => $categories,
                'newId'         => $newId,
            //'clubItems'      => $clubItems,
            );
            if($request->ajax()){
                return Response($res,200);
            }else{
                return View('pages.stock.club_barcode.club_barcode',$res);
            }
        }
        catch(Exception $e){
            Log::error("ClubBarcodeController@index ".$e);
            return View("errors.exception");
        }
    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /*public function store(Request $request)
    {
        $clubItem = $request->all()["itemclub"];
        
        $clubItem = ClubBarcode::create($clubItem);

        if(!is_null($clubItem)){
         $res = array(
            "status"  => "success",
            "msg"   => trans("validation.success_store",[ "attribute" => trans('glob.club_barcode') ])
            );
     }else{
        $res = array(
            "status"  => "Error",
            "msg"   => trans("validation.error_store",[ "attribute" => trans('glob.club_barcode') ])    
            );
    }

    if($request->ajax()){

        return Response($res,200);

    }else{

        ($res["status"] == "success") 
        ? 
        toastr::success('Success', trans("validation.success_store",[ "attribute" => trans('glob.club_barcode') ]))
        :
        toastr::error('error', trans("validation.error_store",[ "attribute" => trans('glob.club_barcode') ]));
        return View("pages.stock.club_barcode.club_barcode",$res);
    }
}*/

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function show($id, Request $request)
    {

        $editClubBarcode= ClubBarcode::where('id', $id)->first(); 

        if($request->ajax()){
            return Response($editClubBarcode,200);
        }else{
            return View('pages.stock.club_barcode.club_barcode',$editClubBarcode);
        }
    }*/

    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function update(Request $request, $id)
    {
        $clubItem = $request->all()["itemclub"];
        
        $clubBarcode = ClubBarcode::find($id);

        if(!is_null($clubBarcode)){

         $clubBarcode->fill($clubItem)->save();

         $res = array(
            "status"  => "success",
            "msg"   => trans("validation.success_update",[ "attribute" => trans('glob.club_barcode') ])
            );
     }
     if($request->ajax()){
        return Response($res,200);
    }else{
        toastr::error('error', trans("validation.error_update",[ "attribute" => trans('glob.club_barcode') ]));
        return View("pages.stock.club_barcode.club_barcode",$res);
    }
}*/

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function destroy($id)
    {
        $clubBarcode  = ClubBarcode::find($id);

        (!is_null($clubBarcode)) ?  ($r = $clubBarcode->delete()) : ($r = null);

        if($r){
            $res = array('status' =>  "success", "data" =>array(), "message" =>trans("validation.success_delete",["attribute" => trans('glob.club_barcode') ]));
        }
        else{
            $res = array('status' =>  "error", "data" =>array(), "message" => trans("validation.error_delete",["attribute" => trans('glob.club_barcode') ]));
        }

        return response($res, 200);
    }*/

    function getItemByName(Request $request){
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
        Log::error("ClubBarcodeController@getItemByName ".$e);
        return View("errors.exception");
    }
}

function getItemByBarcode(Request $request){
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
        Log::error("ClubBarcodeController@getItemByBarcode ".$e);
        return View("errors.exception");
    }

}

}
