<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Option;
use App\Item;
use App\Purchase;
use App\PurchaseReturn;
use App\Billing;

class OptionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $options = Option::all();
            $opt = array();
            $barcode=array();
            $arrDateFormats = array();

            foreach ($options as $o) {

                if($o["value"]=='1' || $o["value"] === '0')
                    $opt[$o["type"]][$o["key"]] = (int)$o["value"];
                else
                   $opt[$o["type"]][$o["key"]] = $o["value"];
           }



     /**
      * barcode type object to push array
      */
     // $arrBarcode[] = (object) array('id' => 'BCGcode128', 'type' => 'BCGcode128');
     // $arrBarcode[]= (object) array('id' => 'BCGcode39extended', 'type' => 'BCGcode39extended');


     /**
      *reports date format object push to array
      */
     // $arrDateFormats[]  = (object) array('formatCode' => 'm/d/Y', 'formatView' => 'o1/31/2018');
     // $arrDateFormats[]  = (object) array('formatCode' => 'm-d-Y', 'formatView' => 'o1-31-2018');
     // $arrDateFormats[]  = (object) array('formatCode' => 'Y/m/d', 'formatView' => '2018/01/31');
     // $arrDateFormats[]  = (object) array('formatCode' => 'Y-m-d', 'formatView' => '2018-01-31');
     // $arrDateFormats[]  = (object) array('formatCode' => 'd-M-Y', 'formatView' => '1-Apr-2018 (Tally Date format)');


     if(count($opt) > 0){
        $res = array(
            "status" => "success",
            "options" => $opt,
            // "arrBarcode"=> $arrBarcode,
            // "arrDateFormats" =>$arrDateFormats,
        );
    }else{
        $res = array(
            "status" => "error",
            "options" => $opt,
            // "barcode"=> $arrBarcode,
            // "arrDateFormats" =>$arrDateFormats,
        );
    }
    
    if($request->ajax()){
        return Response( $res,200);
    }else{
        return View("pages.options.options", ['res'=> $res]);
    }
}
catch(Exception $e){
    Log::error("OptionsController@index ".$e);
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
         $res = $request->all()['keyValue'];

         for($i = 0; $i< count($res); $i++){
             $check = Option::where("type","=",$request->all()['type'])->where("key","=",array_keys($request->all()['keyValue'])[$i])->get();
             if(count($check)){
                \DB::table('options')
                ->where("type","=",$request->all()['type'])
                ->where("key","=",array_keys($request->all()['keyValue'])[$i])  
                ->limit(1) 
                ->update(array(
                    "type" =>  $request->all()["type"], 
                    "key" => array_keys($request->all()['keyValue'])[$i], 
                    "value" => array_values($request->all()['keyValue'])[$i]
                ));
            }else{
             Option::create(array(
                "type" =>  $request->all()["type"],
                "key" => array_keys($request->all()['keyValue'])[$i],
                "value" => array_values($request->all()['keyValue'])[$i]
            ));
         }

     }

     if($request->ajax()){

        return Response($res,200);

    }else{

        ($res["status"] == "success") 
        ? 
        toastr::success('Success', trans("validation.success_store",[ "attribute" => trans('menu.options') ]))
        :
        toastr::error('error', trans("validation.error_store",[ "attribute" => trans('menu.options') ]));
        return View("pages.options.options",$res);
    }
}
catch(Exception $e){
    Log::error("OptionsController@store ".$e);
    return View("errors.exception");

}
}

/** get exist file name and attach number as per count  */
public function checkFileExist( $actual_name,$extension){
    try{
        static $i = 1;
        $actualName = (string)$actual_name.'_'.$i;
        if(file_exists( public_path() . '/img/' .$actualName.".".$extension)){
            $actualName = substr($actualName, 0, strrpos($actualName.".", "_"));
            $i++;
            return $this->checkFileExist( $actualName , $extension);
        }else{
            return $actualName.".".$extension;
        }
    }
    catch(Exception $e){
        Log::error("OptionsController@checkFileExist ".$e);
        return View("errors.exception");

    }
}

    /**
     * uplode item profile function 
     */
    public function uploadLogo(Request $request){
        try{

            $file = $request->file('file');
            $name = $file->getClientOriginalName();
            $reName = str_replace(' ', '_', $name);


            if(file_exists( public_path() . '/img/' .$reName)) {

                $actual_name = pathinfo($reName,PATHINFO_FILENAME);
                $extension = pathinfo($name, PATHINFO_EXTENSION);
                /** call recusive function till get unique file name */
                $name = $this->checkFileExist($actual_name,$extension);
                $file->move(public_path().'/img', $name); 

                $response["status"] = "success";
                $response["imageUrl"] = $name;
                $response["message"] =  trans("validation.success_update",["attribute" => trans('glob.logo') ]);
                return response($response, 200);
            } else {


                $file->move(public_path().'/img', $reName); 

                $response["status"] = "success";
                $response["imageUrl"] = $reName;
                $response["message"] =  trans("validation.success_update",["attribute" => trans('glob.logo') ]);
                return response($response,200);
            }
        }
        catch(Exception $e){
            Log::error("OptionsController@uploadLogo ".$e);
            return View("errors.exception");

        } 
    }
    /**
     * check vat Records validation
     */
    public function checkVatRecords(){
        try{
            $arrItems= Item::all();
            $arrPurchase=Purchase::all();
            $arrPurchaseReturn=PurchaseReturn::all();
            $arrBilling=Billing::all();
            if(sizeof($arrItems)>0 || sizeof($arrPurchase)>0 || sizeof($arrPurchaseReturn)>0 || sizeof($arrBilling)>0){
                $res= array(
                 'status'=>'warning',
                 'message'=>'Please inform the Admin to clean the Database '
             );

            }
            else{
              $res= array(
               'status'=>'success',
               'message'=>'vat Type Add Successfully'
           );
          }
          return Response($res,200);
      }
      catch(Exception $e){
        Log::error("OptionsController@checkVatRecords ".$e);
        return View("errors.exception");

    }
}


}
