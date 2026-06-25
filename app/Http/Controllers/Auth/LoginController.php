<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Option;
use Carbon\Carbon;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    public function showLoginForm()
    {
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
        if((($fromDate <= $date) && ($date <= $toDate)) || ($type == 2 && ($fromDate <= $date))){

          $users = \App\User::where('roleId','<>',1)->get();
          return View('auth.login',["users" => $users]);

        }else{
          $res = array(
            "type" => "expired",
            "shop_name" => $shop_name[0]->value,
            );
          return Redirect('/');

        }

      }else{
        $res = array(
          "type" => "new"
          );

        return Redirect('/');
      }  



    }

  /**
    * this is for login supper admin only
    */
  public function supperLogin(){
    
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
      if((($fromDate <= $date) && ($date <= $toDate)) || ($type == 2 && ($fromDate <= $date))){

        $users = \App\User::all();
        return View('auth.login',["users" => $users]);

      }else{
        $res = array(
          "type" => "expired",
          "shop_name" => $shop_name[0]->value,
          );
        return Redirect('/');

      }

    }else{
      $res = array(
        "type" => "new"
        );

      return Redirect('/');
    }  



  }

  use AuthenticatesUsers;



    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
      $this->middleware('guest', ['except' => 'logout']);
    }

    public function encryptFunc($sData,$Key){  
      $sResult ='';
      for($i=0;$i<strlen($sData);$i++){ 
        $sChar    = substr($sData, $i, 1); 
        $KeyChar = substr($Key, ($i % strlen($Key)) - 1, 1);        
        $sChar    = chr(ord($sChar) + ord($KeyChar));   
        $sResult .= $sChar; 
      } 
      return $this->encode_base64($sResult);
    }   

    public function decryptFunc($dData,$Key){ 
     $sResult = ''; 
     $dData   = $this->decode_base64($dData); 
     for($i=0;$i<strlen($dData);$i++){ 
      $sChar    = substr($dData, $i, 1); 
      $KeyChar = substr($Key, ($i % strlen($Key)) - 1, 1); 
      $sChar    = chr(ord($sChar) - ord($KeyChar)); 
      $sResult .= $sChar; 
    } 
    return $sResult;
  }

  public function encode_base64($sData){ 
    $sBase64 = base64_encode($sData); 
    return strtr($sBase64, '+/', '-_'); 
  } 

  public function decode_base64($dData){ 
    $sBase64 = strtr($dData, '-_', '+/'); 
    return base64_decode($sBase64); 
  } 


}
