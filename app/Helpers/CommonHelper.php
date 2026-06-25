<?php 
namespace App\Helpers;
/**
* This class is created for common functions
* This functions will available through out all controllers			
*/
use Auth;
use Illuminate\Support\Facades\View;
use App\Resource;

class CommonHelper
{
	private $privileges;		

	public function __construct(){
		$this->privileges = Auth::User()->privileges;
	}

  public static function can_view($r){
    $c = new CommonHelper(); 
    $resource = \App\Resource::where("parent_resource","=","0")->where("slug","like","%".$r."%")->get();

    if(count($resource)){
      $id = $resource[0]->id;
      if(isset($c->privileges->groupBy("resourceId")[$id]))
        return ($c->privileges->groupBy("resourceId")[$id][0]->view) ? true : false;
      else
        return false;
    }else{
      return false;
    }
  }
  public static function has_access($resource){
    $billcheck = $resource;

    if($resource == 'billing2' || $resource == 'billing1'){
      $resource = 'billing';
    }

    $c = new CommonHelper(); 



    if($billcheck == 'billing1'){

      $resource = \App\Resource::where("parent_resource","<>","0")->where("slug","like","%".$resource."%")->get();

    }else{

      $resource = \App\Resource::where("slug","like","%".$resource."%")->get();
      
    }



    if(count($resource)){

      $id = $resource[0]->id;
      
      
      if(isset($c->privileges->groupBy("resourceId")[$id]))
        return ($c->privileges->groupBy("resourceId")[$id][0]->view);
      else
        return false;
    }else{
      return false;
    }
  }

  public static function can_create($resource){
    $c = new CommonHelper(); 
    $resource = \App\Resource::where("slug","like","%".$resource."%")->get(); 

    if(count($resource)){
      $id = $resource[0]->id;
      if(isset($c->privileges->groupBy("resourceId")[$id]))
        return ($c->privileges->groupBy("resourceId")[$id][0]->create) ? true : false;
      else
        return false;
    }else{
      return false;
    }
  }

  public static function can_update($resource){

    $c = new CommonHelper(); 
    $resource = \App\Resource::where("slug","like","%".$resource."%")->get();

    if(count($resource)){
      $id = $resource[0]->id;
      if(isset($c->privileges->groupBy("resourceId")[$id]))
        return ($c->privileges->groupBy("resourceId")[$id][0]->update) ? true : false;
      else
        return false;
    }else{
      return false;
    }
  }

  public static function can_delete($resource){

    $c = new CommonHelper(); 
    $resource = \App\Resource::where("slug","like","%".$resource."%")->get();

    if(count($resource)){
      $id = $resource[0]->id;
      if(isset($c->privileges->groupBy("resourceId")[$id]))
        return ($c->privileges->groupBy("resourceId")[$id][0]->delete) ? true : false;
      else
        return false;
    }else{
      return false;
    }
  }

  public static function activeMainMenu($resource){
    $route = \Route::getFacadeRoot()->current()->uri();
    $r = explode("/",$route)[0];
    if($resource == $r){
     echo 'active';
   }
 }

 public static function setActive($resource){
  $route = \Route::getFacadeRoot()->current()->uri();

  if(($resource == $route) || (isset($r[1]) && $r[1] == $route)){
   echo 'class="active"';
 }
}

  /**
   * returns curren autoincrement value of table
   * @param  string $tableName tablename of which you want auto increment value
   * @return integer            autoincrement value
   */
  public static function get_autoIncremenetVal($tableName){
    $database = \DB::getDatabaseName();
    $result =\DB::select("SELECT auto_increment FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$database'  and  table_name = '$tableName' ");
    if(isset($result[0]->auto_increment) && $result[0]->auto_increment !== null){
        return $result[0]->auto_increment;
    }
    // Fallback: get MAX(id) + 1 if INFORMATION_SCHEMA returns NULL
    $maxResult = \DB::select("SELECT IFNULL(MAX(id), 0) + 1 as next_id FROM `$tableName`");
    return (isset($maxResult[0]->next_id)) ? $maxResult[0]->next_id : 1;
  }
  public static function can_option_view($type , $key){

    $c = new CommonHelper(); 
    $check = \App\Option::where("type","=",$type)
    ->where("key","=",$key)
    ->where("value","=","1")
    ->get();
    
    if(count($check)){
      return true ;
    }else{
      return false ;
    }
  }

  public static function can_billscreen_view($type , $key){

    $c = new CommonHelper(); 
    $check = \App\Option::where("type","=",$type)
    ->where("key","=",$key)
    ->get();
    

    if(count($check)){
      return $check[0]->value ;
    }else{
      return false ;
    }
  }

 /**
  * get  Amount Fixed number
  * @param int value
  * @return {int} amount fixed number
  */
 public static function fixIt($value){

  $arrFixAmount= \App\Option::where("key","=",'amount_decimal')->get();
  $intFixAmt=isset($arrFixAmount[0]->value) ? $arrFixAmount[0]->value :config("custom.amt_fix");
  return number_format((float)$value, $intFixAmt, '.', '');
}

 /**
  * get Quantity Fixed  number
  * @param int value
  * @return {int} Quantity fixed number
  */
 
 public static function fixQty($value){
  $arrFixQty=\App\Option::where("key","=",'quantity_decimal')->get();
  $intFixQty=isset($arrFixQty[0]->value) ? $arrFixQty[0]->value:config("custom.qty_fix");
  return number_format((float)$value, $intFixQty, '.', '') ;
}

/**
  * get fix bill number
  * @param int value 
  * @return {int} bill padding fixed number
  */
public static function fixBill($value){
 $arrFixBill=\App\Option::where("key","=",'bill_pad')->get();
 $intFixBill=isset($arrFixBill[0]->value) ? $arrFixBill[0]->value :config("custom.bill_pad");
 $arrBillPrefixWord= \App\Option::where("key","=",'bill_prefix_word')->get();
 $strBillPrefixWord= isset($arrBillPrefixWord[0]->value) ? $arrBillPrefixWord[0]->value :config("custom.bill_prefix_word");
 return  $strBillPrefixWord.str_pad($value, $intFixBill, '0', STR_PAD_LEFT);
}

/**
  * get currancy Symbol
  * @return {string} currancy string
  */
public static function curr(){
  $arrCurrOption= \App\Option::where("key","=","curr")->get();
  $strCurrOptionvalue=isset($arrCurrOption[0]->value)?$arrCurrOption[0]->value:config("custom.curr");
  return  $strCurrOptionvalue;
}

 /**
  * get email options
  * @return {string} Email string
  */
 public static function email(){
  $arremailoption=\App\Option::where("key","=",'email')->where("type","like","%".'setting'."%")->get();
  return isset($arremailoption[0]->value)? $arremailoption[0]->value :config("custom.email");
}

/**
  * get Amount Fixed number for dashboard
  * @return {int} Amount fixed number
  */
public static function dFixIt(){
  $arrFixItOption=\App\Option::where("key","=",'amount_decimal')->where("type","like","%".'setting'."%")->get();
  return isset($arrFixItOption[0]->value) ? $arrFixItOption[0]->value :config("custom.amt_fix");
}

/**
  * get Quantity Fixed  number for dashboard
  * @return  {int} Quantity fixed number
  */
public static function dQty(){
  $arrQtyOption=\App\Option::where("key","=",'quantity_decimal')->where("type","like","%".'setting'."%")->get();
  return isset($arrQtyOption[0]->value)? $arrQtyOption[0]->value:config("custom.qty_fix");
}

/**
  * get Bill Paddingd number for dashboard
  * @return {int} Bill padding number
  */
public static function dBill(){
  $arrBillOption=\App\Option::where("key","=",'bill_pad')->where("type","like","%".'setting'."%")->get();
  return isset($arrBillOption[0]->value)? $arrBillOption[0]->value:config("custom.bill_pad");
}


      /**
        * get Dateformat for reports (View/PDF/excel/print)
        * @return {string} 
        */
      public static function rDateFormat(){

        $arrRDateFormatOption=\App\Option::where("key","=",'reports_date_format')->where("type","like","%".'setting'."%")->get();
        return isset($arrRDateFormatOption[0]->value) ? ($arrRDateFormatOption[0]->value !='' || $arrRDateFormatOption[0]->value !=null) ? $arrRDateFormatOption[0]->value :  config("custom.date_format") : config("custom.date_format");
      }

      public static function rDateFormatSQL($dateFormat){

        $tempRDateFormatSQL = '%d/%m/%Y';

        switch ($dateFormat) {
          case "m/d/Y":
          $tempRDateFormatSQL = '%m/%d/%Y';
          break;
          case "d/m/Y":
          $tempRDateFormatSQL = '%d/%m/%Y';
          case "m-d-Y":
          break;
          $tempRDateFormatSQL = '%m-%d-%Y';
          break;
          case 'Y/m/d':
          $tempRDateFormatSQL = '%Y/%m/%d';
          break;
          case 'Y-m-d':
          $tempRDateFormatSQL = '%Y-%m-%d';
          break;
          case 'd-M-Y':
          $tempRDateFormatSQL = '%d-%b-%Y';
          break;

          default:
          $tempRDateFormatSQL = '%d/%m/%Y';
          break;
        }

        return $tempRDateFormatSQL;
      }


      /**
  * get vat_type 
  * @return {int} 
  */
      public static function vatType(){
        $arrVatType=\App\Option::where("key","=",'vat_type')->where("type","like","%".'vat'."%")->get();
        return isset($arrVatType[0]->value)? $arrVatType[0]->value:config("custom.vat_type");
      }



    }

