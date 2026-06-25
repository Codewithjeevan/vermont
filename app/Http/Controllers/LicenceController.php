<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Option;
use Carbon\Carbon;

use Exception;
use Log;

class LicenceController extends Controller
{        

    /** function for generating client key */
    public function generateClientKey(){ 
        try{   
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < 10 ; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            $randomString;
            $check = Option::where("type","=","pos")->where("key","=","pos_key")->get();
            if(count($check)){
                \DB::table('options')
                ->where("type","=","pos")
                ->where("key","=","pos_key")  
                ->limit(1) 
                ->update(array(
                    "type" => 'pos' ,
                    "key" => 'pos_key',
                    "value" => $randomString,
                    ));
            }else{
                Option::create(array(
                    "type" => 'pos' ,
                    "key" => 'pos_key',
                    "value" => $randomString,
                    ));
            }
            return $res = array(
                "status" => "0",
                "message" => 'Success',
                "pos_key" => $randomString,
                );

        }catch(Exception $e){
            Log::error("LicenceController@generateClientKey ".$e);
            return View("errors.exception");
        }
    }

    public function storeDetails(Request $request){
        try{
            $dataArray = $request->all()["data"]; 
            $pos_secret = $dataArray['pos_secret'];
            $shop_name = $dataArray['shop_name'];
            

            $check_secret = Option::where("type","=","pos")->where("key","=","pos_secret")->get();
            if(count($check_secret)){
                \DB::table('options')
                ->where("type","=","pos")
                ->where("key","=","pos_secret")  
                ->limit(1) 
                ->update(array(
                    "type" => 'pos' ,
                    "key" => 'pos_secret',
                    "value" => $pos_secret,
                    ));
            }else{
                Option::create(array(
                    "type" => 'pos' ,
                    "key" => 'pos_secret',
                    "value" => $pos_secret,
                    ));
            }

            $check_shop_name = Option::where("type","=","pos")->where("key","=","shop_name")->get();
            if(count($check_shop_name)){
                \DB::table('options')
                ->where("type","=","pos")
                ->where("key","=","shop_name")  
                ->limit(1) 
                ->update(array(
                    "type" => 'pos' ,
                    "key" => 'shop_name',
                    "value" => $shop_name,
                    ));
            }else{
                Option::create(array(
                    "type" => 'pos' ,
                    "key" => 'shop_name',
                    "value" => $shop_name,
                    ));
            }

            return $res = array(
                "status" => "0",
                "message" => 'Success',
                );

        }catch(Exception $e){
            Log::error("LicenceController@storeDetails ".$e);
            return View("errors.exception");
        }
    }

    public function getDetails(){
        try{
            $pos_key = Option::where("type","=","pos")->where("key","=","pos_key")->get();
            $pos_secret = Option::where("type","=","pos")->where("key","=","pos_secret")->get();
            $shop_name = Option::where("type","=","pos")->where("key","=","shop_name")->get();
            
            $data = [];
            if(count($pos_key) && count($pos_secret) && count($shop_name)){
                $data['pos_key'] = $pos_key[0]->value;
                $data['pos_secret'] = $pos_secret[0]->value;
                $data['shop_name'] = $shop_name[0]->value;
                
                return $res = array(
                    "status" => "0",
                    "message" => 'Success',
                    "data" => $data,
                    );
            }else{
                $data['pos_key'] = '';
                $data['pos_secret'] = '';
                $data['shop_name'] = '';

                return $res = array(
                    "status" => "0",
                    "message" => 'Success',
                    "data" => $data,
                    );
            }

        }catch(Exception $e){
            Log::error("LicenceController@getDetails ".$e);
            return View("errors.exception");
        }
    }
}
