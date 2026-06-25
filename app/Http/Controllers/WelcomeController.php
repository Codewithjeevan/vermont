<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Option;
use App\PlanetAuth;
use Carbon\Carbon;

use Exception;
use Log;

class WelcomeController extends Controller
{        

  public function validateKey(){ 
    try{
      $pos_key = Option::where("type","=","pos")->where("key","=","pos_key")->get();
      $pos_secret = Option::where("type","=","pos")->where("key","=","pos_secret")->get();
      $shop_name = Option::where("type","=","pos")->where("key","=","shop_name")->get();


      if(count($pos_key) && count($pos_secret) && count($shop_name)){

        $sResult = $this->decryptFunc($pos_secret[0]->value,$pos_key[0]->value); 
        $type =  $startDateString = substr($sResult, 10, 1);
        $startDateString = substr($sResult, 11, 10);
        $stopDateString = substr($sResult, 21, 10);
        $fromDate = date('Y-m-d', strtotime($startDateString));
        $toDate = date('Y-m-d', strtotime($stopDateString));
        $date = Carbon::now()->format('Y-m-d');
        if((($fromDate <= $date) && ($date <= $toDate)) || $type == 2){
         $res = array(
          "type" => "valid",
          );
       }else{
        $res = array(
          "type" => "expired",
          "shop_name" => $shop_name[0]->value,
          );
      }

    }else{
      $res = array(
        "type" => "new"
        );
    }  

    return View("welcome",$res);
  }catch(Exception $e){
    Log::error("WelcomeController@validateKey ".$e);
    return View("errors.exception");
  }
}

public function encryptFunc($sData,$Key){  
  try{
    $sResult ='';
    for($i=0;$i<strlen($sData);$i++){ 
      $sChar    = substr($sData, $i, 1); 
      $KeyChar = substr($Key, ($i % strlen($Key)) - 1, 1);        
      $sChar    = chr(ord($sChar) + ord($KeyChar));   
      $sResult .= $sChar; 
    } 
    return $this->encode_base64($sResult);
  }catch(Exception $e){
    Log::error("WelcomeController@encryptFunc ".$e);
    return View("errors.exception");
  }
}   

public function decryptFunc($dData,$Key){ 
  try{
   $sResult = ''; 
   $dData   = $this->decode_base64($dData); 
   for($i=0;$i<strlen($dData);$i++){ 
    $sChar    = substr($dData, $i, 1); 
    $KeyChar = substr($Key, ($i % strlen($Key)) - 1, 1); 
    $sChar    = chr(ord($sChar) - ord($KeyChar)); 
    $sResult .= $sChar; 
  } 
  return $sResult;

}catch(Exception $e){
  Log::error("WelcomeController@decryptFunc ".$e);
  return View("errors.exception");
}
}

public function encode_base64($sData){ 
  try{
    $sBase64 = base64_encode($sData); 
    return strtr($sBase64, '+/', '-_'); 
  }catch(Exception $e){
    Log::error("WelcomeController@encode_base64 ".$e);
    return View("errors.exception");
  }
} 

public function decode_base64($dData){ 
  try{
    $sBase64 = strtr($dData, '-_', '+/'); 
    return base64_decode($sBase64); 
  }catch(Exception $e){
    Log::error("WelcomeController@decode_base64 ".$e);
    return View("errors.exception");
  }
}   

public function getPlanetAuthCredential(){
  try{
   return $credentials=PlanetAuth::first();

  }
  catch(Exception $e){
    Log::error("WelcomeController@getPlanetAuthCredential".$e);
    return View("errors.exception");
  }
}

public function planetLogin(Request $request){
  try{
 
// $url="https://auth.qa-tax.planetpayment.ae/auth?client_id=$request->client_id&client_secret=$request->client_secret/auth";

// $ch = curl_init();
// curl_setopt($ch, CURLOPT_URL, $url);

// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
// curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                                                                                                       
//   'Content-Type: text/html; charset=utf-8')                                                                       
// );
// $response = curl_exec($ch);
// if ($response === false) {
//   $response = curl_error($ch);
// }
// return stripslashes($response);
/**test url start */
//$url = "https://auth.qa-tax.planetpayment.ae/auth?client_id=$request->client_id&client_secret=$request->client_secret/auth";
/**test url End */

$url = "https://auth.tax.planetpayment.ae/auth?client_id=$request->client_id&client_secret=$request->client_secret/auth";
 
  $ch = curl_init();
 
  //next we have to set options so below is the options for the curl
  $array_options = array(
     
    // $url is the variable we want to fetch using CURLOPT_URL
    CURLOPT_URL=>$url,
 
    //setting curl option RETURNTRANSFER to true so
    //that it returns the response
    //instead of outputting it
    CURLOPT_RETURNTRANSFER=>true,
  );
 
  //setting multiple options using curl_setopt_array
  curl_setopt_array($ch,$array_options);
 
  // using curl_exec() is used to execute the POST request
  $resp = curl_exec($ch);
 
  // decode the response
  $final_decoded_data = json_decode($resp,true);
    print_r($final_decoded_data);
 
  //close the cURL and load the page
  curl_close($ch);
  }
  catch(Exception $e){
    Log::error("WelcomeController@planetLogin ".$e);
    return View("errors.exception");
  }
}

public function PlanetAccessToken(Request $request){
  try{
    $client_id="";
    $client_secret="";
    $grant_type="";
 
     if($request->data!=null){
     
         $client_id=$request->data['client_id'];
        $client_secret=$request->data['client_secret'];
         $grant_type=$request->data['grant_type'];
     
     }
     else{
       
         $client_id =$request->client_id;
         $client_secret =$request->client_secret;
         $grant_type=$request->grant_type;
       
     }
//$url="https://auth.qa-tax.planetpayment.ae/auth/realms/planet/protocol/openid-connect/token";
$url="https://auth.tax.planetpayment.ae/auth/realms/planet/protocol/openid-connect/token";
$data   =array(
        'client_id' =>$request->client_id,
        'client_secret' =>$request->client_secret,
        'grant_type' =>$request->grant_type
);

           $send = json_encode([$data]);
           $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
        
            );

 $ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('application/x-www-form-urlencoded'));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS,
http_build_query(
  array(
    'client_id' =>$client_id,
    'client_secret' =>$client_secret,
    'grant_type' =>$grant_type
  )));

$httpCode = curl_getinfo($ch , CURLINFO_HTTP_CODE); // this results 0 every time
$response = curl_exec($ch);

if ($response === false) 
    $response = curl_error($ch);

return stripslashes($response);

//curl_close($ch);



}
  catch(Exception $e){
    Log::error("WelcomeController@PlanetAccessToken ".$e);
    return View("errors.exception");
  }
}



}
