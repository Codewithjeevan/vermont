<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Loyalty;
use \Toastr;
use Common;
class LoyaltyController extends Controller
{

    /**
     * This is initial function for loading view and setting initial values
     * @return View 
     */
    public function index(){
        try{
           $objLoyalty = Loyalty::where("key","=","loyalty_activation")->first();

           if(is_null($objLoyalty)){
              $l = new Loyalty();
              $l->fields;
          }else{
              $l = new Loyalty();
              $l->fields["loyalty_activation"] = Common::fixIt(Loyalty::where("key","=","loyalty_activation")->first()->value);
              $l->fields["loyalty_points_on_sale"] = Common::fixIt(Loyalty::where("key","=","loyalty_points_on_sale")->first()->value);
              $l->fields["loyalty_points"] = Loyalty::where("key","=","loyalty_points")->first()->value;
              $l->fields["loyalty_redeem_maturity"] = Loyalty::where("key","=","loyalty_redeem_maturity")->first()->value;
              $l->fields["loyalty_on_redeem_points"] = Loyalty::where("key","=","loyalty_on_redeem_points")->first()->value;
              $l->fields["loyalty_on_redeem_discount"] = Loyalty::where("key","=","loyalty_on_redeem_discount")->first()->value;
          }
          return View("pages.loyalty.loyalty", [
              "loyalty_fields"=> $l->fields
          ]);
      }
      catch(Exception $e){
        Log::error("LoyaltyController@index ".$e);
        return View("errors.exception");
    }
}

    /**
     * This will update the loyalty details
     * @param  Request $request request should contain all the loyalty parameter
     * @return view          
     */
    public function update(Request $request){
        try{

           $anyRes[0] = $this->updateLoyaltyDetails($request, "loyalty_activation");
           $anyRes[1] = $this->updateLoyaltyDetails($request, "loyalty_points_on_sale");
           $anyRes[2] = $this->updateLoyaltyDetails($request, "loyalty_points");
           $anyRes[3] = $this->updateLoyaltyDetails($request, "loyalty_redeem_maturity");
           $anyRes[4] = $this->updateLoyaltyDetails($request, "loyalty_on_redeem_points");
           $anyRes[5] = $this->updateLoyaltyDetails($request, "loyalty_on_redeem_discount");

           if($anyRes[0] && $anyRes[1] && $anyRes[2] && $anyRes[3] && $anyRes[4] && $anyRes[5]){
              toastr::success('Success', trans("validation.success_update",[ "attribute" => trans('glob.loyalty') ]));
          }else{
              toastr::success('Error', "There is issue with updating" . trans('glob.loyalty'));
          }
          return redirect()->action('LoyaltyController@index');
      }
      catch(Exception $e){
        Log::error("LoyaltyController@update ".$e);
        return View("errors.exception");
    }
}

    /**
     * This is helper function for updating options of loyalty
     * @param  array $request 
     * @param  string $key     
     * @return boolean         
     */
    private function updateLoyaltyDetails($request, $key){
        try{
           $objLoyalty = Loyalty::where("key","=","$key")->first();
           if(is_null($objLoyalty)){
              $anyRes = $this->insertLoyaltyDetails($request, $key);
          }else{
              $objLoyalty->key = $key;
              $objLoyalty->value = $request[$key];
              $anyRes = $objLoyalty->save();
          }
          return $anyRes; 
      }
      catch(Exception $e){
        Log::error("LoyaltyController@updateLoyaltyDetails ".$e);
        return View("errors.exception");
    }
}

    /**
     * This is helper function for inserting data of loyalty
     * @param  array $request 
     * @param  string $key     
     * @return boolean          
     */
    private function insertLoyaltyDetails($request, $key){
        try{
           $objLoyalty = Loyalty::create([
              "key" => $key,
              "value" => $request[$key]
          ]);

           return (is_null($objLoyalty)) ? false : true;
       }
       catch(Exception $e){
        Log::error("LoyaltyController@insertLoyaltyDetails ".$e);
        return View("errors.exception");
    }
    
}

}
