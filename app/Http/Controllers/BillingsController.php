<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
use Carbon\Carbon;
use App\Customer;
use App\Category;
use App\Billing;
use App\BillingItems;
use App\XYPromotion;
use App\Package;
use App\Stock;
use App\Salesman;
use App\BillReturns;
use App\BillReturnItems;
use App\BillingHomeDelivery;
use App\User;
use App\zSales;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Hash;
use App\BillingDelete;
use Common;
use Excel;
use PDF;
use Illuminate\Support\Facades\Input;
use App\Loyalty;
use App\Currency;
use App\CountryCode;
use App\BillingTaxRefund;

use Exception;
use Log;
// use Illuminate\Support\Facades\Cookie;
// use Illuminate\Support\Facades\Crypt;


class BillingsController extends Controller
{

    private $response;
    private $rDateFormat;
    private $rDateFormatSQL;
    private $bill_pad;
    private $intVatType;

    public function __construct(){
        $this->response = array(
            "status" => "success",
            "data" => [],
            "message" => ""
        );
        $this->rDateFormat = Common::rDateFormat();
        $this->rDateFormatSQL = Common::rDateFormatSQL($this->rDateFormat);
        $this->bill_pad = common::dBill();
        $this->intVatType = common::vatType();
    }

    /**
     * Create billing item object as it was not as per need we made it as per requirement
     * @param  Object $billItemObj Billingitem object
     * @param  Object $itemObj     Item Object
     * @return Object              Merged Laravel Billing Object
     */
    public function makeBillingItemObject($billItemObj, $itemObj){

        try{

            $billItemObj['existId']     =   $billItemObj['id'];
            $billItemObj['id']     =   $itemObj->id;
            $billItemObj['productCode']     =   $itemObj->productCode;
            $billItemObj['serial_number']   =   $itemObj->serial_number;
            $billItemObj[ 'category']       =   $itemObj->category;
            $billItemObj[ 'categories']       =   $itemObj->categories;
            $billItemObj[ 'barcode']        =   $itemObj->barcode;
            $billItemObj[ 'itemName']       =   $itemObj->itemName;
            $billItemObj['itemNameArabic']  =   $itemObj->itemNameArabic;
            $billItemObj[ 'costPrise']      =   $itemObj->costPrise;
            $billItemObj[ 'salePrise']      =   $itemObj->salePrise;
            $billItemObj['wholesale_salePrise']   =   $itemObj->wholesale_salePrise;
            $billItemObj[ 'vat']      =   $billItemObj["vat"];
            $billItemObj[ 'description']    =   $itemObj->description;
            $billItemObj[ 'unit']           =   $itemObj->unit;
            $billItemObj[ 'stock']          =   $itemObj->stock;
            $billItemObj[ 'min']            =   $itemObj->min;
            $billItemObj[ 'max']            =   $itemObj->max;
            $billItemObj['expiryChk']       =   $itemObj->expiryChk;
            $billItemObj['packageChk']      =   $itemObj->packageChk;
            $billItemObj['item_with_box']   =   $itemObj->item_with_box;
            $billItemObj['expiry_date']     =   $itemObj->expiry_date;
            $billItemObj[ "image"]          =   $itemObj->image;
            $billItemObj["promotion_from"]  =   $itemObj->promotion_from;
            $billItemObj["promotion_to"]    =   $itemObj->promotion_to;
            $billItemObj["promotion_price"] =   $itemObj->promotion_price;
            $billItemObj["x_y_prmoChk"]     =   $itemObj->x_y_prmoChk;
            $billItemObj["offer"]           =   $itemObj->offer;
            $billItemObj["package_list"]    =   $itemObj->packageList;
            $billItemObj["units"]           =   $itemObj->units;
            $billItemObj["itemType"]        =   $billItemObj["offer_type"];
            $billItemObj["has_xyoffer"]     =   $itemObj["has_xyoffer"];
            $billItemObj["is_promotion"]    =   $itemObj["is_promotion"];
            $billItemObj["has_package"]     =   $itemObj["has_package"];
            $billItemObj["serial_no"]     =   $billItemObj["serial_no"];
            $billItemObj["serial_check"]     =   $billItemObj["serial_check"];


            return $billItemObj;

        }catch(Exception $e){
            Log::error("BillingsController@makeBillingItemObject ".$e);
            return View("errors.exception");
        }
    }



    /**
     * Apply Promotional price by checking date and criteria
     * We changed sale prise to promotional prise id it is in pramotion criteria
     * @param  Array $items Array of Items
     * @return Array  Items with promotional prise
     */
    public function applyOffers($items){


        try{

            foreach ($items as $item) {


                $item["itemType"] = 0;
                $item["has_xyoffer"] = 0;
                $item["is_promotion"] = 0 ;
                $item["has_package"] = 0;

                if($item["serial_no"]==null || $item["serial_no"]==''){
                    $item["serial_no"] = ''; 
                }

                if($item["serial_check"]==null || $item["serial_check"] ==''){
                    $item["serial_check"] = '';
                }

                $item['existId'] = 0;




                if(isset($item["offerwith"])){
                    if($item["offerwith"]==0 || $item["offerwith"]=='' || $item["offerwith"]==null){
                        $item["offerwith"] = 0;
                    }

                }else{
                    $item["offerwith"] = 0;
                }


                if($item["promotion_from"] !=null && $item["promotion_to"] !=null){
                    if((Carbon::parse($item["promotion_from"]) <= Carbon::now()) && (Carbon::parse($item["promotion_to"]) >= Carbon::now() )){
                        $item["salePrise"] = $item["promotion_price"];
                        $item["is_promotion"] = 1;
                    }
                }
                if($item->offer !=null && $item->offer["prmo_from"] !=null && $item->offer["prmo_to"] !=null){

                    if((Carbon::parse($item->offer["prmo_from"]) <= Carbon::now()) && (Carbon::parse($item->offer["prmo_to"]) >= Carbon::now()) && $item->x_y_prmoChk==1){
                        $item["has_xyoffer"] = 1;
                    }
                }
                if($item->packageChk==1){
                    $item["has_package"] = 1;
                }

            }
            return $items;

        }catch(Exception $e){
            Log::error("BillingsController@applyOffers ".$e);
            return View("errors.exception");
        }

    }



    /**
     * Customer details are not in bill object so we added as per client syntax
     * @param Object $bill Bill
     */
    public function addCustomerDetails($bill){
        try{
             
            $loyalty = Loyalty::all();
            $temp =array();
            $message ='';
            $eligible = '';
            $message_point = '';
            $customerId = 0;
            $intNewId = \Common::get_autoIncremenetVal("customers");

            if($bill->_customer!=null){
                if($bill->homeDeliveryCustomer!=null){
                    $customerId = (isset($bill->homeDeliveryCustomer->customer_id)) ? $bill->homeDeliveryCustomer->customer_id :0;
                }
                else{
                    $customerId = (isset($bill->_customer->id)) ? $bill->_customer->id:0;
                }
                $arrCustData = Customer::with('countryCode')->find($customerId);
                if(!empty($arrCustData) && !empty($loyalty) && ($bill->earned_points != 0 or $bill->loyalty_discount !=0)){
                    $intPurchaseAmount = 0;

                    $billAmt_ = Billing::where('customer_id','=',$customerId)
                    ->sum('bil_amt');

                    $billReturnAmt_ = BillReturns::join('billings','billings.id','bill_returns.billing_id')
                    ->where('billings.customer_id','=',$customerId)
                    ->sum('bill_returns.amount');
                    $intPurchaseAmount = ($billAmt_ - $billReturnAmt_);


                    if($intPurchaseAmount >= $loyalty[0]->value && $arrCustData->eligibility == 1){
                        $eligible = 'Loyalty Eligible';
                        if($arrCustData->points > $loyalty[3]->value){
                            $intReddemablePoints = Common::fixIt($arrCustData->points - $loyalty[3]->value);
                            $message_point = "";
                            $message = "Points = ".Common::fixIt($arrCustData->points);
                        }
                        else{
                            $intPintsNeeded = Common::fixIt($loyalty[3]->value - $arrCustData->points);
                            if($intPintsNeeded >= 1){
                                $message = "Points ="." ". $intPintsNeeded. " More to Redeem";
                            }
                            else{
                                $message = "";
                            }
                            $message_point = "Total Points = ".Common::fixIt($arrCustData->points);
                        }
                    }
                    else{
                        $eligible = 'Loyalty Not Eligible';
                        $intPurchaseNeeded = Common::fixIt($loyalty[0]->value - $intPurchaseAmount);
                        $message_point =  "Total Purchase ="." AED  ".Common::fixIt($intPurchaseAmount);
                        $message = "Purchase ="." AED  ".$intPurchaseNeeded ." More to Eligible";
                    }
                }

                if($bill->homeDeliveryCustomer!=null){
                    $arrCustData = Customer::find($bill->homeDeliveryCustomer->customer_id);
                    $temp['id'] = $bill->homeDeliveryCustomer->customer_id;
                    $temp['customerNumber'] = $bill->_customer->mobile;
                    $temp['customerName']  =$bill->homeDeliveryCustomer->name;
                    $temp['email'] = $bill->homeDeliveryCustomer->email;
                    $temp['address']  =$bill->homeDeliveryCustomer->address;
                    $temp['flat_no'] = $bill->homeDeliveryCustomer->flat_no;
                    $temp['blg'] = $bill->homeDeliveryCustomer->blg;
                    $temp['street'] = $bill->homeDeliveryCustomer->street;
                    $temp['eligibility'] = $arrCustData->eligibility;
                    $temp['points'] = $arrCustData->points;
                    $temp['message'] = $message ;
                    $temp['message_point'] = $message_point;
                    $temp['eligible'] =  $eligible;
                    $temp['age_group'] =  $bill->_customer->age_group;
                    $temp['gender'] =  $bill->_customer->gender;
                    $temp['city'] =  $bill->_customer->city;
                    $temp['newcustomerId'] =  $intNewId;


                }
                else{

                    $arrCustData = Customer::with('countryCode')->find($bill->_customer->id);
                    $temp['id'] = $bill->_customer->id;
                    $temp['customerName'] = $bill->_customer->name;
                    $temp['customerNumber'] = $bill->_customer->mobile;
                    $temp['email'] = $bill->_customer->email;  
                    $temp['address']  =$bill->_customer->address;
                    $temp['flat_no'] = '';
                    $temp['blg'] = '';
                    $temp['street'] = '';
                    $temp['eligibility'] = $arrCustData->eligibility;
                    $temp['points'] = $arrCustData->points;
                    $temp['message'] = $message ;
                    $temp['message_point'] = $message_point;
                    $temp['eligible'] =  $eligible;
                    $temp['age_group'] = $bill->_customer->age_group;
                    $temp['gender'] = $bill->_customer->gender;
                    $temp['city'] = $bill->_customer->city;
                    $temp['birthday']=date('Y-m-d', strtotime($bill->_customer->birthday));
                    if($bill->_customer->nationality!=null && $bill->_customer->nationality!=0){
                        $temp['nationality']=$bill->_customer->countrycode->code2;
                    }
                    else{
                        $temp['nationality']='';
                    }
                   if($bill->_customer->countryOfResidence!=null && $bill->_customer->countryOfResidence!=0){
                    $temp['countryOfResidence']=$bill->_customer->countrycode->code2;
                   }
                   else{
                    $temp['countryOfResidence']='';
                   }
                   
                    $temp['type']=$bill->_customer->type;
                    $temp['issuedBy']=$bill->_customer->issuedBy;
                    $temp['number']=$bill->_customer->number;
                    $temp['newcustomerId'] =  $intNewId;
                    $temp['firstName']=$bill->_customer->firstName;
                    $temp['lastName']=$bill->_customer->lastName;
                }

            }else{
                $temp['id'] = '';
                $temp['customerName'] = '';
                $temp['customerNumber'] = '';
                $temp['email'] = ''; 
                $temp['address']  ='';
                $temp['flat_no'] = '';
                $temp['blg'] = '';
                $temp['street'] = '';
                $temp['eligibility'] = 0;
                $temp['points'] = 0;
                $temp['message'] = '' ;
                $temp['message_point'] = '';
                $temp['eligible'] = '';
                $temp['age_group'] = '';
                $temp['gender'] = '';
                $temp['city'] = '';
                $temp['birthday']='';
                    $temp['nationality']='';
                    $temp['countryOfResidence']='';
                    $temp['type']='';
                    $temp['issuedBy']='';
                    $temp['number']='';
                    $temp['firstName']='';
                    $temp['lastName']='';
            }
            $bill['customer']=$temp;
            return $bill;

        }catch(Exception $e){
            Log::error("BillingsController@addCustomerDetails ".$e);
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
                "sr_no" => trans('billing.sr_no'),
                "dis_amt" => trans('billing.dis_amt'),
                "sub_total" => trans('billing.sub_total'),
                "thank_you" => trans('glob.thank_you'),
                "salesman" => trans('glob.salesman'),
                "customer" => trans('glob.customer'),
                "mobile"    => trans('glob.mobile'),
                "pay_mode"  => trans('glob.pay_mode'),
                "paid"  => trans('glob.paid'),
                "balance"   => trans('billing.balance'),
                "phone" => trans('glob.phone'),
                "net"   => trans('glob.net'),
                "card"   => trans('glob.card'),
                "tnc"    => trans('glob.tnc'), 
                "website" => trans('glob.website'), 
                "count" => trans('glob.count'), 
                "cashier" => trans('glob.cashier'),  
                "total_arebic" => trans('glob.total_arebic'),
                "loyalty_discount" => trans('billing.loyalty_discount'),
                "redeemed_points" => trans('billing.redeemed_points'),
                "v_amt" => trans('billing.v_amt'),
                "tax_amt" => trans('glob.tax_amt'),
                "vat_amt" => trans('glob.vat_amt'),
            )
        );
            return $labels;
        }catch(Exception $e){
            Log::error("BillingsController@getLabels ".$e);
            return View("errors.exception");
        }
    }

    /**
     * This will return Customer ID if customer is not present this will create new customer and return customer id
     * @param  Object $customer Customer object got from Request
     * @return ineteger           Customer id
     */
    public function getCustomerId($customer){

        try{
           
            $_cust = Customer::where("mobile", "=", $customer["customerNumber"])->get();
            $cust = $_cust->first();
            $nationality='';
            $countryOfResidence='';
            if($customer["nationality"]!==null && $customer["nationality"]!==0){
                $nationality1=CountryCode::where('code','=', $customer["nationality"])->orWhere('code2','=',$customer["nationality"])->first();
                $nationality=$nationality1->id;
              }
              else{
                $nationality=null;
              }
              if($customer["countryOfResidence"]!==null && $customer["countryOfResidence"]!==0)
              {
               $countryOfResidence1=CountryCode::where('code','=', $customer["countryOfResidence"])->orWhere('code2','=', $customer["countryOfResidence"])->first();
               $countryOfResidence=$countryOfResidence1->id;
              }
              else{
                $countryOfResidence=null;
              }

            if(is_null($cust)){

                $address = '';
                $email='';
              

                if($customer["address"]!=null || $customer["address"]!=''){
                    $address =$customer["address"];
                }else{
                    $address =null;
                }
                if($customer["email"]!==null || $customer["email"]!==''){
                    $email=$customer["email"];
                }else{
                    $email=null;
                }
              
               
                $newcust = Customer::create(array(
                    "name" => $customer["customerName"],
                    "birthday"=> $customer["birthday"],
                    "mobile" => $customer["customerNumber"],
                    "address" => $address,
                    "email" => $email,
                    "age_group" => isset($customer["age_group"]) ? $customer["age_group"] : '' ,
                    "gender" => isset($customer["gender"]) ? $customer["gender"] : '' ,
                    "city" => isset($customer["city"]) ? $customer["city"] : '' ,
                    "nationality"=>$nationality,
                    "countryOfResidence"=>$countryOfResidence,
                    "type"=>isset($customer["type"]) ? $customer["type"] : '' ,
                    "number"=>isset($customer["number"]) ? $customer["number"] : '' ,
                    "issuedBy"=>isset($customer["issuedBy"]) ? $customer["issuedBy"] : '' ,
                    "firstName"=>isset($customer["firstName"]) ? $customer["firstName"] : '' ,
                    "lastName"=>isset($customer["lastName"]) ? $customer["lastName"] : '' 
                ));
                return $newcust->id;

            }else{
                $cust['email']= $customer['email'];
                $cust["birthday"]= $customer["birthday"];
                $cust['address'] = $customer['address'];
                $cust['age_group'] = $customer['age_group'];
                $cust['gender'] = $customer['gender'];
                $cust['city'] = $customer['city'];
                $cust["nationality"]=$nationality;
                $cust["countryOfResidence"]=$countryOfResidence;
                $cust["type"]=isset($customer["type"]) ? $customer["type"] : '' ;
                $cust["number"]=isset($customer["number"]) ? $customer["number"] : '' ;
                $cust["issuedBy"]=isset($customer["issuedBy"]) ? $customer["issuedBy"] : '';
                $cust["firstName"]=isset($customer["firstName"]) ? $customer["firstName"] : '' ;
                $cust["lastName"]=isset($customer["lastName"]) ? $customer["lastName"] : '' ;
                $cust['name'] = $customer['customerName'];
                $cust->save();
                return $cust->id;
            }

        }catch(Exception $e){
            Log::error("BillingsController@getCustomerId ".$e);
            return View("errors.exception");
        }
    }

    public function is_in_pramotion($i){
        try{
            $is_in_promo = 0;
            if($i["promotion_from"] !=null && $i["promotion_to"] !=null){
                if((Carbon::parse($i["promotion_from"]) <= Carbon::now()) && (Carbon::parse($i["promotion_to"]) >= Carbon::now() )){
                    $is_in_promo =1;
                }
            }

            return $is_in_promo;

        }catch(Exception $e){
            Log::error("BillingsController@is_in_pramotion ".$e);
            return View("errors.exception");
        }
    }

    /**
    * to calculate qty of bill item aginst return bill items
    * @param  object $billItems   billing item
    * @param  object $returnItems return item
    * 
    */
    public function getQtyBillToReturn($billItems, $returnItems){
        try{

            foreach ($billItems->itemList as $billItem) {
                $billItem['qtyReturn'] = 0;
                if(count($returnItems)!=0){


                    $retrunItemsQty = DB::select( "SELECT SUM(bri.qty) as qty FROM   billing_items bi 
                        INNER JOIN   ( bill_returns br   INNER JOIN     bill_return_items bri  
                        ON br.id = bri.return_id   ) 
                        ON   bi.id = bri.existId
                        WHERE   br.billing_id =  $billItems->id
                        AND  bri.item_id = $billItem->item_id 
                        AND  bri.existId = $billItem->id 
                        GROUP BY bri.item_id, bri.serial_no, bri.offer_type, bri.existId");

                    if(count($retrunItemsQty)){
                        $billItem['qtyReturn'] = $retrunItemsQty[0]->qty;
                    }


                }
            }

            return $billItems;

        }catch(Exception $e){
            Log::error("BillingsController@getQtyBillToReturn ".$e);
            return View("errors.exception");
        }
    }
    /**
     * to get user privileges for get all bill or individual Bill
     * 
     */
    public function getAutAccess($Request){
        try{

            $slug = $Request->segment(2);

            if($Request->ajax()){
                if($Request->segment(2) == 'billing'){
                    $slug = 'billing1';
                }
            }
            return \Common::has_access($slug);

        }catch(Exception $e){
            Log::error("BillingsController@getAutAccess ".$e);
            return View("errors.exception");
        }

    }

    /**
     * get all home delivery bills, those are not payed yet...
     * @return object  object of all billing home delivery 
     */
    public function getHomeBillAll(Request $Request){
        try{
            $userId=1;
            if (Auth::check())
                {
                    $userId = \Auth::id();
                }

                /** for set privileges for get all bill or individual Bill i.e 2 for all bill  */
                if($this->getAutAccess($Request) == 2){
                    $homeBilling = Billing::select('id')->where("home_delivery", "=", "1")->where("balance", "!=", "0")->orderBy("id", "desc")->get();
                    foreach ($homeBilling as $objBill) {
                        $objBill['str_billing_id'] = \Common::fixBill($objBill->id);
                    }
                }else if($this->getAutAccess($Request) == 1){
                    $homeBilling = Billing::select('id')->where("home_delivery", "=", "1")->where("balance", "!=", "0")->where('user_id','=',$userId)->orderBy("id", "desc")->get();
                    foreach ($homeBilling as $objBill) {
                        $objBill['str_billing_id'] = \Common::fixBill($objBill->id);
                    }
                }else{
                    $homeBilling = [];
                }


                return Response($homeBilling,200);

            }catch(Exception $e){
                Log::error("BillingsController@getHomeBillAll ".$e);
                return View("errors.exception");
            }
        }

    /**
     * get all home delivery bills, those are not payed yet...
     * @return object  object of all billing home delivery 
     */
    public function getHoldBillAll(Request $Request){
        try{
            $userId=1;
            if (Auth::check())
                {
                    $userId = \Auth::id();

                }


                /** for set privileges for get all bill or individual Bill i.e 2 for all boll  */
                if($this->getAutAccess($Request) == 2){

                    $homeBilling = Billing::select('id')->where("billing_status", "like", "hold")->orderBy("id", "desc")->get();
                    foreach ($homeBilling as $objBill) {
                        $objBill['str_billing_id'] = \Common::fixBill($objBill->id);
                    }
                }else if($this->getAutAccess($Request) == 1){
                    $homeBilling = Billing::select('id')->where("billing_status", "like", "hold")->where('user_id','=',$userId)->orderBy("id", "desc")->get();

                    foreach ($homeBilling as $objBill) {
                        $objBill['str_billing_id'] = \Common::fixBill($objBill->id);
                    }
                }else{
                    $homeBilling = [];
                }



                return Response($homeBilling,200);

            }catch(Exception $e){
                Log::error("BillingsController@getHoldBillAll ".$e);
                return View("errors.exception");
            }
        }
    /**
     * This function contains all the existing bill details
     * This should call after every operation and send as response so that We will have updated copy of bills 
     * and items
     * @return Array Response return object
     */
    public function getAllBillingDetails($Request){

        try{

            $userId=1;
            if (Auth::check())
                {
                    $user = Auth::user();
                    $maxDiscount = $user->role->max_discount; 
                    $roleId = $user->roleId;
                    $userId = \Auth::id();
                }

                $ai = \Common::get_autoIncremenetVal("billings");
                $intNewId = \Common::get_autoIncremenetVal("customers");
                $returnBill = \Common::get_autoIncremenetVal("bill_returns");

                $salesmas = Salesman::orderBy('name')->get();
                $BarcodeOrSNoStatus = (\Common::can_option_view('billing','serial_number') && \Common::can_option_view('billing','barcode_add'));

                $loyalty = Loyalty::all();


        /**
         * check billing 1 or billing 2 
         * $items is the array of boxed items which are only need when billing 2 is enabled else 
         * no need to query the items
         */
        if(\Common::can_billscreen_view('billing','billscreen') == 2){
            $items = Item::with(["units",'categories',"offer.item.units","packageList.items.units"])->where('item_with_box','=',1)->orderBy('itemName')->get();
            /**
             * Applied pramotional price if date is in between pramotion date
             * Applied XY offer if applicable
             * is_promotion and has_xyoffer proprties added to the item  object
             */
            $items = $this->applyOffers($items);
        }else{
            $items =[];
        }



        /** @var obejct return all labels for pdf generation  */
        $labels = $this->getLabels();

        /*reportData*/
        $mallDetails = $this->mallDetails();

        $boolcustomerCreateCheck = Common::can_create('customer');
        $boolcustomerUpdateCheck = Common::can_update('customer');

        $arrGender = array();
        //$arrGender[] = (object) array('id'=>'0','name' => 'Gender');
        $arrGender[] = (object) array('id'=>'1','name' => 'Male');
        $arrGender[] = (object) array('id'=>'2','name' => 'Female');

        $arrAgeGroup = array();
        //$arrAgeGroup[] = (object) array('id'=>'0','name' => 'Age Group');
        $arrAgeGroup[] = (object) array('id'=>'1','name' => '18 to 29');
        $arrAgeGroup[] = (object) array('id'=>'2','name' => '30 to 36');
        $arrAgeGroup[] = (object) array('id'=>'3','name' => '37 to 45');
        $arrAgeGroup[] = (object) array('id'=>'4','name' => '46 to 55');
        $arrAgeGroup[] = (object) array('id'=>'5','name' => '56+');

        $arrCurrency = Currency::all();


        $res = array(
            "items" => $items,
            "ai" => $ai,
            "date" => Carbon::now()->format('d M Y'),
            "labels" => $labels,
            "salesmas" => $salesmas,

            'BarcodeOrSNoStatus' => $BarcodeOrSNoStatus,
            "dateReport" => Carbon::now()->format('m/d/Y'),
            "dateReport1" => Carbon::now()->format('Y-m-d'),
            "mallDetails" => $mallDetails,

            "returnId" => $returnBill,
            
            "maxDiscount" =>$maxDiscount,
            "role"         => $roleId,
            "loyalty"      => $loyalty,
            "boolcustomerCreateCheck"=>$boolcustomerCreateCheck,
            "boolcustomerUpdateCheck"=>$boolcustomerUpdateCheck,
            'newcustomerId' =>$intNewId,
            "arrGender"  =>  $arrGender,
            "arrAgeGroup"  =>  $arrAgeGroup,
            "arrCurrency"   =>  $arrCurrency,
        );

        return $res;

    }catch(Exception $e){
        Log::error("BillingsController@getAllBillingDetails ".$e);
        return View("errors.exception");
    }

}


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $Request)
    {
        try{
            $res= $this->getAllBillingDetails($Request);
            $this->response["data"] = $res;
            if($Request->ajax()){
                return Response($this->response,200);
            }else{
                return View("pages.billing.billing", $res);
            }

        }catch(Exception $e){
            Log::error("BillingsController@index ".$e);
            return View("errors.exception");
        }
    }  

    /**
     * This is same as index method only View redirection is different
     * @param  Request $Request $Request
     * @return View           response
     */
    public function billing2Index(Request $Request){

        try{
            $res= $this->getAllBillingDetails($Request);
            $this->response["data"] = $res;


            if($Request->ajax()){
                return Response($this->response,200);
            }else{
                return View("pages.billing.billing-with-boxes", $res);
            }

        }catch(Exception $e){
            Log::error("BillingsController@billing2Index ".$e);
            return View("errors.exception");
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $Request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $Request)
    {
         
        try{
            if (Auth::check())
            {
                $userId = \Auth::id();
            }else{
                $userId = 1;
            }

            $reqPara = $Request->all()["billing"];
            $token=$Request->all()["_token"];

            if($reqPara["tax_refund_flag"]==0){
               
            $reqPara["addressUpdateAtHome"] = ( ($reqPara["addressUpdateAtHome"]=="true"|| $reqPara["addressUpdateAtHome"]== 1 ) ? true : false );

                if( $reqPara["addressUpdateAtHome"] ){
                
                    $customerData = Customer::find($reqPara["customer"]["id"]);
                    if(!is_null($customerData)){
                        $customerData->fill([
                            "address"=>$reqPara["customer"]["address"]
                        ])->save();
                        $reqPara["customer_id"] = $customerData->id;
                    }else{
                       
                        if($reqPara["customer"]["customerNumber"]!=null){
                            $reqPara["customer_id"] = $this->getCustomerId($reqPara["customer"]);
                        }else{
                            $reqPara["customer_id"] = null;
                        }
                    }
                }else{
                    if($reqPara["customer"]["customerNumber"]!=null){
                     $reqPara["customer_id"] = $this->getCustomerId($reqPara["customer"]);
                    }else{
                        $reqPara["customer_id"] = null;
                    }
                }

                $billing_id = Billing::insertGetId(array(
                    "billing_status" => $reqPara["billing_status"],
                    "invoice_type" => $reqPara["invoice_type"],
                    "payment_mode" => $reqPara["payment_mode"],
                    "card_no" => $reqPara["card_no"],
                    "reference_no" => $reqPara["reference_no"],
                    "customer_id" => $reqPara["customer_id"],
                    "subtotal" => $reqPara["subtotal"], 
                    "loyalty_points" => $reqPara["loyalty_points"], 
                    "loyalty_discount" => $reqPara["loyalty_discount"],
                    "earned_points" => $reqPara["earned_points"], 
                    "dis_amt" => $reqPara["dis_amt"],
                    "v_amt" => $reqPara["v_amt"],
                    "cash_amt" => $reqPara["cash_amt"],
                    "card_amt" => $reqPara["card_amt"],
                    "bil_amt" => $reqPara["bil_amt"],
                    "currency" => isset($reqPara["currency"]) ? $reqPara["currency"] : 0,
                    "received" => $reqPara["received"],
                    "balance" => $reqPara["balance"],
                    "salesman_id"=>$reqPara["salesman_id"],
                    "user_id" => $userId,
                    "home_delivery" => $reqPara["home_delivery"],
                    "tax_refund_flag"=>$reqPara["tax_refund_flag"],
                    "created_at" => Carbon::now()
                ));

                if($reqPara["home_delivery"]){
                    $home_cust = $Request->all()["billing"]['customer'];
                    if($home_cust["id"]==null || $home_cust["id"]==''){
                        if($reqPara["customer_id"]!=null){
                          $home_cust["id"] = $reqPara["customer_id"];
                      }

                  }
                  if($home_cust["id"]!=null &&  $billing_id!==null && $home_cust["address"]!=null ){
                    $bill_home_delivery = BillingHomeDelivery::create(array(
                        "customer_id" => $home_cust["id"],
                        "billing_id" => $billing_id,
                        "name" => $home_cust["customerName"],
                        "email" => $home_cust["email"],
                        "address" => $home_cust["address"],
                        "flat_no" => $home_cust["flat_no"],
                        "blg" => $home_cust["blg"],
                        "street" => $home_cust["street"],
                    )); 
                }
            }


            $items = $reqPara["items"];


            foreach ($items as $i) {
                $is_in_promo = $this->is_in_pramotion($i);

                $objNewBill = BillingItems::create(array(
                    "billing_id" => $billing_id,
                    "item_id" =>  $i["id"],
                    "serial_no" => $i["serial_no"],
                    "qty" => $i["qty"],
                    "rate" => $i["rate"],
                    "vat" => $i["vat"],
                    "offer_type" => $i["itemType"],
                    "is_promotion" =>  $is_in_promo,
                    "offerwith" => $i["offerwith"],
                    "serial_check" => $i["serial_check"],
                ));

                if($reqPara["billing_status"]!="hold"){
                /**
                 * has_package and has_xyoffer will not be in offer item and package items
                 * as they are taken from relation and we have not added  these properties to relation
                 */
                $intBillItemId = 0;
                if($objNewBill != null){
                    $intBillItemId = $objNewBill->id;
                }
                
                $updatedStock = $this->updateItemStock("insert", $i, null, $billing_id, $intBillItemId);

            }
        }
       
        $this->saveLoyaltyPoints($reqPara["customer_id"],$billing_id);
        $printData = $this->savePrint($Request,$billing_id);

        $labels = $this->getLabels();

        return Response(array(
            "status" => "success",
            "labels" => $labels,
            "datetime" => Carbon::now()->format('d-m-Y H:i'),
            'getBill' => $printData["getBill"],
            'getreturnBill' => $printData["getreturnBill"],
            'arrVatSummary' => $printData["arrVatSummary"],   
            
        ));
 

            }

            else{
 
                
                 $items = $reqPara["items"];
                 $items1=[];
       foreach($items as $i){
        $newItem = array(
        'grossAmount'=>$i['grossAmount'],
        'code'=>"",
        'departmentCode'=>"",
        'netAmount'=>$i['netAmount'],
        'description'=>$i['itemName'],
        'discountAmount'=>"",
        'quantity'=>$i['qty'],
        'serialNumber'=>"",
        'unitPrice'=>$i['salePrise'],
        'vatRate'=>$i['vat'],
       'vatCode'=>"5",
       'vatAmount'=>$i['vatAmount'],
       'merchandiseGroup'=>$i['merchandiseGroup'],
       'taxRefundEligible'=>true
        );
        array_push($items1, $newItem);
       }
         $data= array(
        'issueTaxRefundTag'=>true,
        'date'=>Carbon::today()->toDateString(),
        "receiptNumber"=>"0".$reqPara['id'],
        "merchantId"=>$reqPara['merchantId'],
        "terminal"=>$reqPara['terminal'],
        "taxFreeId"=>null,
        "type"=>"RECEIPT",
        "operator"=>array(
            "id"=> null,
            "code"=> "operator code",
            "name"=> "operator name"
        ), 
        "order"=>array(
            "totalBeforeVAT"=>$reqPara['planetsubtotal'],
            "vatIncl"=>$reqPara['planetvatamt'],
            "total"=>$reqPara['bil_amt'],
            "items"=>$items1,
        ),
             "paymentMethods"=>array(
                "code"=>$reqPara['paymentMethodCode'],
                "name"=>'',
                "amount"=>$reqPara['bil_amt']
             ),
           "shopper"=>array(
            "code"=>'',
            "firstName"=>$reqPara["customer"]['firstName'],
             "lastName"=>$reqPara["customer"]['lastName'],
             "nationality"=>$reqPara["customer"]['nationality'],
             "countryOfResidence"=>$reqPara["customer"]['countryOfResidence'],
             "phoneNumber"=>$reqPara["customer"]['customerNumber'],
             "birth"=>array(
                "date"=>   (new Carbon($reqPara["customer"]['birthday']))->format('Y-m-d'),
                "country"=> $reqPara["customer"]['nationality']
             ),
                "shopperIdentityDocument"=>array(
                    
                    "expirationDate"=>"",
                     "type"=>strtoupper($reqPara["customer"]['type']),
                    "issuedBy"=>$reqPara["customer"]['issuedBy'],
                     "number"=>$reqPara["customer"]['number'],
                )


                )

            );
           // $url="https://frontoffice.qa-tax.planetpayment.ae/services/transactions/api/v2/new-transaction";
            $url="https://frontoffice.tax.planetpayment.ae/services/transactions/api/v2/new-transaction";
            $authorization = "Authorization:".$reqPara['bearerToken'];
            $token1='X-CSRF-TOKEN:'.$token;
            // $cookies="Cookie: X-CSRF-Token=$token";
          //  $csrfToken = Crypt::decrypt(Cookie::get('XSRF-TOKEN'));
           // $cookies= "X-XSRF-TOKEN: $csrfToken";
            $send=json_encode($data);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10000);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10000);
            curl_setopt($ch, CURLOPT_POST,1 );
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json',$authorization));
          //   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$send);
          
            $httpCode = curl_getinfo($ch ,CURLINFO_HTTP_CODE); // this results 0 every time
            $str = curl_exec($ch);
       
            if ($str=== false) 
                $str = curl_error($ch);
                $response1=json_decode($str,true);

             if($response1["code"]==50 || $response1["code"]==22 || $response1["code"]==400 || $response1["code"]==27 || $response1["code"]==37|| $response1["code"]==20 || $response1["code"]==23 || $response1["code"]==43 || $response1["code"]==313 || $response1==null || $response1["code"]!=12)
             {
               
                return   Response(array(
                    "status" =>"warning",
                    "code"=>$response1["code"],
                    "message"=>$response1["message"],
                    'vatRefund'=>$response1
                ));
             }
             else{
                $reqPara["addressUpdateAtHome"] = ( ($reqPara["addressUpdateAtHome"]=="true"|| $reqPara["addressUpdateAtHome"]== 1 ) ? true : false );

                if( $reqPara["addressUpdateAtHome"] ){

                    $customerData = Customer::find($reqPara["customer"]["id"]);
                    if(!is_null($customerData)){
                        $customerData->fill([
                            "address"=>$reqPara["customer"]["address"]
                        ])->save();
                        $reqPara["customer_id"] = $customerData->id;
                    }else{
                        if($reqPara["customer"]["customerNumber"]!=null){
                            $reqPara["customer_id"] = $this->getCustomerId($reqPara["customer"]);
                        }else{
                            $reqPara["customer_id"] = null;
                        }
                    }
                }else{
                    if($reqPara["customer"]["customerNumber"]!=null){
                        $reqPara["customer_id"] = $this->getCustomerId($reqPara["customer"]);
                    }else{
                        $reqPara["customer_id"] = null;
                    }
                }
                $billing_id = Billing::insertGetId(array(
                    "billing_status" => $reqPara["billing_status"],
                    "invoice_type" => $reqPara["invoice_type"],
                    "payment_mode" => $reqPara["payment_mode"],
                    "card_no" => $reqPara["card_no"],
                    "reference_no" => $reqPara["reference_no"],
                    "customer_id" => $reqPara["customer_id"],
                    "subtotal" => $reqPara["subtotal"], 
                    "loyalty_points" => $reqPara["loyalty_points"], 
                    "loyalty_discount" => $reqPara["loyalty_discount"],
                    "earned_points" => $reqPara["earned_points"], 
                    "dis_amt" => $reqPara["dis_amt"],
                    "v_amt" => $reqPara["v_amt"],
                    "cash_amt" => $reqPara["cash_amt"],
                    "card_amt" => $reqPara["card_amt"],
                    "bil_amt" => $reqPara["bil_amt"],
                    "currency" => isset($reqPara["currency"]) ? $reqPara["currency"] : 0,
                    "received" => $reqPara["received"],
                    "balance" => $reqPara["balance"],
                    "salesman_id"=>$reqPara["salesman_id"],
                    "user_id" => $userId,
                    "home_delivery" => $reqPara["home_delivery"],
                    "tax_refund_flag"=>$reqPara["tax_refund_flag"],
                    "created_at" => Carbon::now()
                ));

                if($reqPara["home_delivery"]){
                    $home_cust = $Request->all()["billing"]['customer'];
                    if($home_cust["id"]==null || $home_cust["id"]==''){
                        if($reqPara["customer_id"]!=null){
                          $home_cust["id"] = $reqPara["customer_id"];
                      }

                  }
                  if($home_cust["id"]!=null &&  $billing_id!==null && $home_cust["address"]!=null ){
                    $bill_home_delivery = BillingHomeDelivery::create(array(
                        "customer_id" => $home_cust["id"],
                        "billing_id" => $billing_id,
                        "name" => $home_cust["customerName"],
                        "email" => $home_cust["email"],
                        "address" => $home_cust["address"],
                        "flat_no" => $home_cust["flat_no"],
                        "blg" => $home_cust["blg"],
                        "street" => $home_cust["street"],
                    )); 
                }
            }


            $items = $reqPara["items"];


            foreach ($items as $i) {
                $is_in_promo = $this->is_in_pramotion($i);

                $objNewBill = BillingItems::create(array(
                    "billing_id" => $billing_id,
                    "item_id" =>  $i["id"],
                    "serial_no" => $i["serial_no"],
                    "qty" => $i["qty"],
                    "rate" => $i["rate"],
                    "vat" => $i["vat"],
                    "offer_type" => $i["itemType"],
                    "is_promotion" =>  $is_in_promo,
                    "offerwith" => $i["offerwith"],
                    "serial_check" => $i["serial_check"],
                ));

                if($reqPara["billing_status"]!="hold"){
                /**
                 * has_package and has_xyoffer will not be in offer item and package items
                 * as they are taken from relation and we have not added  these properties to relation
                 */
                $intBillItemId = 0;
                if($objNewBill != null){
                    $intBillItemId = $objNewBill->id;
                }
                
                $updatedStock = $this->updateItemStock("insert", $i, null, $billing_id, $intBillItemId);

            }
        }
        $vatReturnRefund = BillingTaxRefund::create(array(
            "billId" => $billing_id,
            "receiptNumber"=>$response1['receiptNumber'],
            "totalAmount"=> $response1['totalAmount'],
            "vatAmount" => $response1['vatAmount'],
            "taxRefundAmount" => $response1["taxRefundAmount"],
            "taxRegisterStatus" => $response1['taxRegisterResponse']["taxRegisterStatus"],
            "taxRegisterTagNumber" => $response1['taxRegisterResponse']["taxRegisterTagNumber"],
            "taxRefundTagNumber" => $response1['taxRefundResponse']["taxRefundTagNumber"],
            "taxRefundStatus"=>$response1['taxRefundResponse']['taxRefundStatus'],
            "taxRefundQrCode"=>$response1['taxRefundResponse']['taxRefundQrCode'],
            "refundAmount"=>$response1['taxRefundResponse']['refundAmount'],
        ));
        $this->saveLoyaltyPoints($reqPara["customer_id"],$billing_id);
        $printData = $this->savePrint($Request,$billing_id);

        $labels = $this->getLabels();
     return   Response(array(
            "status" => "success",
            "code"=>$response1["code"],
            "message"=>$response1["message"],
            "labels" => $labels,
            "datetime" => Carbon::now()->format('d-m-Y H:i'),
            'getBill' => $printData["getBill"],
            'getreturnBill' => $printData["getreturnBill"],
            'arrVatSummary' => $printData["arrVatSummary"],   
            'vatRefund'=>$response1
        ));
             }
            

        
          



                
            }


            /** get login user id  */
            
    }
    catch(Exception $e){
        Log::error("BillingsController@store ".$e);
        return View("errors.exception");
    }

}


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $Request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $Request, $id){

        try{

         $reqPara = $Request->all()["billing"];
         $objBilling = [];

         $reqPara["addressUpdateAtHome"] = ( ($reqPara["addressUpdateAtHome"]=="true"|| $reqPara["addressUpdateAtHome"]== 1 ) ? true : false );


        /**
         * Customer details fetched 
         */
        
        if( $reqPara["addressUpdateAtHome"] ){


            $customerData = Customer::find($reqPara["customer"]["id"]);

            if(!is_null($customerData)){
                $customerData->fill([
                    "address"=>$reqPara["customer"]["address"]
                ])->save();
            }else{
                if($reqPara["customer"]["customerNumber"]!=null){
                    $reqPara["customer_id"] = $this->getCustomerId($reqPara["customer"]);
                }else{
                    $reqPara["customer_id"] = null;
                }
            }
        }else {

            if($reqPara["customer"]["customerNumber"]!=null){
                $reqPara["customer_id"] = $this->getCustomerId($reqPara["customer"]);
            }else{
                $reqPara["customer_id"] = null;
            }
        }
        
        $billing_ =  Billing::find($id);
        $objBilling = Billing::find($id);
        $billing_->fill($reqPara);
        $billing_->save();


        if($reqPara["home_delivery"]){

            $home_cust = $Request->all()["billing"]['customer'];

            $getbilling =  Billing::find($id);
            $billId = $getbilling->id;


            if($home_cust["id"]==null || $home_cust["id"]==''){
                if($reqPara["customer_id"]!=null){
                    $home_cust["id"] = $reqPara["customer_id"];
                }
            }



            if($home_cust["id"]!=null &&  $billing_['id']!==null && $home_cust["address"]!=null ){

                $billingHome =  BillingHomeDelivery::where('customer_id' , '=', $home_cust["id"])
                ->where('billing_id' , '=', $billId)
                ->first();

                if(!is_null($billingHome)){

                    \DB::table('billing_home_deliveries')
                    ->where('billing_id' , '=', $billId)
                    ->where('customer_id' , '=', $home_cust["id"])
                    ->update(array(
                        "name" => $home_cust["customerName"],
                        "email" => $home_cust["email"],
                        "address" => $home_cust["address"],
                        "flat_no" => $home_cust["flat_no"],
                        "blg" => $home_cust["blg"],
                        "street" => $home_cust["street"],
                    ));
                }else{
                    $bill_home_delivery = BillingHomeDelivery::create(array(
                        "customer_id" => $home_cust["id"],
                        "billing_id" => $billId,
                        "name" => $home_cust["customerName"],
                        "email" => $home_cust["email"],
                        "address" => $home_cust["address"],
                        "flat_no" => $home_cust["flat_no"],
                        "blg" => $home_cust["blg"],
                        "street" => $home_cust["street"],
                    )); 
                }

            }
        }

        $items = $reqPara["items"];

        $allBilliItems_ = BillingItems::where("billing_id", "=", $id)->get();
        

        /**
         * Checked if item was in database and deleted from the billing items then it should get deleted from billing items
         * and stock should get updated
         */
        
        foreach ($allBilliItems_ as $b) {

            $flg = 0; 
            foreach ($items as $i) {
                if(isset($i['existId'])){
                    if($b->id == $i['existId']){
                        $flg++;
                        break;
                    }
                }
            }

            if($flg == 0){
                $item["id"] = $b->item_id;
                $item["qty"] = $b->qty;
                $item["itemType"] = $b->offer_type;
                $this->updateItemStock("delete", $item, null, $id, $b->id);
                $b->delete();
            }
        }
        
        


        /**
         * Now this will update all biling items that are matching with before edit and after edit
         */
        foreach ($items as $i) {

            if(isset($i['existId'])){
                $billingItems_ = BillingItems::where("item_id", "=", $i['id'])->where("billing_id", "=", $id)
                ->where("serial_no","=",$i["serial_no"])
                ->where("serial_check","=",$i["serial_check"])
                ->where("id", "=", $i['existId']);
                $billingItem = $billingItems_->get();
            }

            
            if(count($billingItem) && isset($i['existId'])){

                /**
                * Delete existing billing items
                */

                
                /** @var Boolean checked item is in promotion */

                $is_in_promo = $this->is_in_pramotion($i);


                /** Add Newly All Items */

                $insertedBillingItems = BillingItems::create(array(
                    "billing_id" => $id,
                    "item_id" =>  $i["id"],
                    "serial_no" => $i["serial_no"],
                    "qty" => $i["qty"],
                    "rate" => $i["rate"],
                    "vat" => $i["vat"],
                    "offer_type" => $i["itemType"],
                    "is_promotion" =>  $is_in_promo,
                    "offerwith" => $i['offerwith'],
                    "serial_check" => $i["serial_check"],
                ));



                /**
                * If billing status is not hold update the stock
                */
                
                if($reqPara["billing_status"]!="hold"){
                    if($reqPara["billing_status"]=="unhold"){

                        $updatedStock = $this->updateItemStock("insert", $i, null, $id, $insertedBillingItems->id);
                        
                    }else{

                     $this->updateItemStock("update", $i, $billingItem, $id, $insertedBillingItems->id);
                 }
             }
             $intBillingItemId = $billingItems_->get();
             DB::table('bill_return_items')
             ->where('existId', $intBillingItemId[0]->id)
             ->update(['existId' => $insertedBillingItems->id]);


             $billingItems_->delete();




         }else{


                /**
                 * has_package and has_xyoffer will not be in offer item and package items
                 * as they are taken from relation and we have not added  these properties to relation
                 */
                

                /** @var Boolean checked item is in promotion */

                $is_in_promo = $this->is_in_pramotion($i);


                /** Add Newly All Items */

                $insertedBillingItems =BillingItems::create(array(

                    "billing_id" => $id,
                    "item_id" =>  $i["id"],
                    "serial_no" => $i["serial_no"],
                    "qty" => $i["qty"],
                    "rate" => $i["rate"],
                    "vat" => $i["vat"],
                    "offer_type" => $i["itemType"],
                    "is_promotion" =>  $is_in_promo,
                    "offerwith" => $i['offerwith'],
                    "serial_check" => $i["serial_check"],
                ));

                /** at the time of hold bill if add new item and still hold status
                 * then add this item in to hold bill and stock will not change , 
                 * previously below if condition is for $is_in_promo to $updateStock 
                 * whole else part in this below if condition  omkesh-13-6-2017 */
                if($reqPara["billing_status"]!="hold"){
                    $updatedStock = $this->updateItemStock("insert", $i, null, $id, $insertedBillingItems->id);
                }
            }


        }

        $this->updateLoyaltyPoints($reqPara["customer_id"],$id,$objBilling);
        $printData = $this->savePrint($Request,$id);

        /** if billing status is unhold then update as create  */
        if($reqPara["billing_status"]=='unhold'){
            $billing_status_update =  Billing::find($id);
            // if()
            $billing_status_update->fill(array(
                'billing_status'  => 'create'
            ));

            $billing_status_update->save();

            return Response(array(
                "status" => "success",
                "message" => "Bill Created successfully",
                "datetime" => Carbon::now()->format('d-m-Y H:i'),
                'getBill' => $printData["getBill"],
                'getreturnBill' => $printData["getreturnBill"],
                'arrVatSummary' => $printData["arrVatSummary"], 
            ));
        }else{
         return Response(array(
            "status" => "success",
            "message" => "Bill updated successfully",
            "datetime" => Carbon::now()->format('d-m-Y H:i'),
            'getBill' => $printData["getBill"],
            'getreturnBill' => $printData["getreturnBill"],
            'arrVatSummary' => $printData["arrVatSummary"], 
        ));
     }

 }catch(Exception $e){
    Log::error("BillingsController@update ".$e);
    return View("errors.exception");
}


}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $Request)
    {

        try{

            $data =  $Request->all()["id"];
            if($data['type']=='billing'){

                /** get user id, user role and check entered password is correct or not  */
                if (Auth::check()){
                    $userId = \Auth::id();
                }else{
                    $userId = 1;
                }
                if($data['role'] > 2){
                    if(!($this->checkAuthDelete($data['pass'])) ){
                        return Response(array(
                            "status" => "errorPass",
                            "message" => "Wrong password",
                        ));
                    }
                }

                $billId = $data['id'];

                $billing_ =  Billing::with('taxRefund')->where("id",'=',$billId)->first();

                $billing_tax_refund=BillingTaxRefund::where("billId",'=',$billId)->first();

                $billingItem_ = BillingItems::where("billing_id", "=", $billId)->get();
            /**
             * Update the stock of billing items
             */
            
            
            if($billing_->tax_refund_flag==0){
                foreach ($billingItem_ as $bill) {

                    $item["id"] = $bill->item_id;
                    $item["qty"] = $bill->qty;
                    $item["itemType"] = $bill->offer_type;
                    if($billing_->billing_status!="hold" ){
    
                        $this->updateItemStock("void", $item, null, $billId, $bill->id);
                    }
                }
                $billDeleteInfo = BillingDelete::create(array(
                    "bill_id" => $billing_->id,
                    "amount" =>  $billing_->bil_amt,
                    "user_id" =>  $userId,
                    "remark" => $data['remark'],
                    "taxRefundTagNumber"=>''
                ));
            }
            else{
                
                $arraydata= array('tagNumber'=>$billing_->taxRefund->taxRefundTagNumber,
            'note'=>$data['remark']);
                //$authorization=$request->data['bearerToken'];
                //$url="https://frontoffice.qa-tax.planetpayment.ae/services/transactions/api/v2/cancel-tax-refund-transaction";
                $url="https://frontoffice.tax.planetpayment.ae/services/transactions/api/v2/cancel-tax-refund-transaction";
             $authorization = "Authorization:".$data['bearerToken'];
                $send=json_encode($arraydata);
               //
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10000);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10000);
                curl_setopt($ch, CURLOPT_POST,1 );
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json',$authorization ));
              //   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_POSTFIELDS,$send);
                  
                $httpCode = curl_getinfo($ch , CURLINFO_HTTP_CODE); // this results 0 every time
                $response = curl_exec($ch);
                
                if ($response === false) 
                    $response = curl_error($ch);
                    //   return stripslashes($response);
                foreach ($billingItem_ as $bill) {

                    $item["id"] = $bill->item_id;
                    $item["qty"] = $bill->qty;
                    $item["itemType"] = $bill->offer_type;
                    if($billing_->billing_status!="hold" ){
    
                        $this->updateItemStock("void", $item, null, $billId, $bill->id);
                    }
                }

                $billDeleteInfo = BillingDelete::create(array(
                    "bill_id" => $billing_->id,
                    "amount" =>  $billing_->bil_amt,
                    "user_id" =>  $userId,
                    "remark" => $data['remark'],
                    "taxRefundTagNumber"=>$billing_->taxRefund->taxRefundTagNumber,
                ));
                $billing_tax_refund->delete();
            }
            

            /**
             * Delete the items
             */
          
            $billing_->delete();
            


            /**
             * Return updated details as response
             */

            if($Request->ajax()){
                return Response(array(
                    "status" => "success",
                    "message" => "Bill deleted successfully",
                ));
            }

        }else if($data['type']=='return'){

           $returnId = $data['id'];

            /**
            * Delete the items in return 
            */
            $this->destroyReturnBill($returnId);

            /**
            * Return updated details as response
            */



            if($Request->ajax()){
                return Response(array(
                    "status" => "success",
                    "message" => "Bill deleted successfully",
                ));
            }

        }else{

            return Response(array(
                "status" => "Error",
                "message" => "Error while deleting bill",
            ));
        }

    }catch(Exception $e){
        Log::error("BillingsController@destroy ".$e);
        return View("errors.exception");
    }
}

    /**
     * delete return bill 
     * @param  int $id return id
     * @return object     return deleted bill item object
     */
    public function destroyReturnBill($id){

        try{

            $rBilling_ =  BillReturns::find($id);

            $returnItems = BillReturnItems::where('return_id','=',$id)->get();

            if($rBilling_!=null ){
                foreach ($returnItems as $destroyRItems) {

                    $rItems['id'] = $destroyRItems["item_id"];
                    $rItems['qty'] = $destroyRItems["qty"];
                    $rItems['itemType'] = $destroyRItems["offer_type"];
                    $this->updateItemStockOnReturn("delete", $rItems, $rBilling_->id, $destroyRItems->id);                
                }
            }
            BillReturnItems::where('return_id', '=', $id)->delete();
            $rBilling_->delete();
            return $rBilling_;

        }catch(Exception $e){
            Log::error("BillingsController@destroyReturnBill ".$e);
            return View("errors.exception");
        }
    }


    /**
     * save return bill all details on  
     * @param  Request $Request [description]
     * @return [type]           [description]
     */
    public function saveReturn(Request $Request){

        try{
            $req = $Request->all()['data'];

            $return_id = BillReturns::insertGetId(array(
                "billing_id" => $req["billing_id"],
                'amount'    => $req["amount"],
                'created_at'    => Carbon::now()->format('Y-m-d H:i:s'),
            ));

            $items = $req["return_items"];

            foreach ($items as $i) {
                if(!isset($i['existId'])){
                   $i["existId"]=0;
               }
               $saveReturn = BillReturnItems::create(array(
                "return_id" => $return_id,
                "item_id" =>  $i["id"],
                "serial_no" => $i["serial_no"],
                "qty" => $i["qty"],
                "offer_type" => $i["itemType"],
                "rate" => $i["rate"],
                "vat" => $i["vat"],
                "existId" => $i["existId"],
            ));
               $intReturnBillingId = 0;
               if($saveReturn !=null){
                $intReturnBillingId = $saveReturn->id;
            }

            $saveReturn['id'] = $saveReturn["item_id"];
            $this->updateItemStockOnReturn("insert", $saveReturn, $saveReturn['return_id'], $intReturnBillingId);

        }


        /**
        * Return updated details as response
        */

        /*--------Get Existing Return Bill for print Receipt-----------*/
        $itemReturn = [];

        $itemReturn = BillReturns::with(['rItem','bill', "rItem.items.units",'rItem.items.categories',"rItem.items.offer.item.units","rItem.items.packageList.items.units",'bill._customer'])
        ->where('bill_returns.id','=', $return_id)
        ->select(['bill_returns.*', 'bill_returns.id as retid'])
        ->first();
        


        if(!empty($itemReturn)){

            /** Added dist_amt for calculation of totalAmount in vuejs */
            $itemReturn = $this->addCustomerDetails($itemReturn);
            $itemReturn['dis_amt']=0;
            $itemReturn->billdatetime =  Carbon::createFromFormat('Y-m-d H:i:s', $itemReturn->created_at)->format($this->rDateFormat.' H:i');
            foreach ($itemReturn->rItem as $rItem) {
                $rItem =$this->makeBillingItemObject($rItem, $rItem->items);
            }
        }
        /*-------------------*/

        $arrBill = Billing::where('id','=',$req["billing_id"])->first();

        $this->saveReturnLoyaltyPoints($arrBill->customer_id,$req["billing_id"]);

        /*$arrBillVatSummary = BillReturnItems::where('return_id','=', $return_id)
        ->groupBy('vat')
        ->selectRaw('sum( ((rate*100)/(100+vat)) * qty) as sale_amt , sum(rate * qty) as net_amt , vat as vat_percent , ((sum( (rate - ((rate*100)/(100 + vat))) * (qty)))) as tax_amt' )
        ->get();*/


        if($this->intVatType != 2){
            $arrBillVatSummary = BillReturnItems::join('bill_returns','bill_returns.id', '=','bill_return_items.return_id' )
            ->join('billings','billings.id', '=','bill_returns.billing_id' )
            ->where('bill_return_items.return_id','=',  $return_id)
            ->groupBy('bill_return_items.vat')
            ->selectRaw('sum( (bill_return_items.rate - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)) - (((bill_return_items.rate) - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt))) - ((((bill_return_items.rate) - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(bill_return_items.vat)))) ) * bill_return_items.qty) as sale_amt , sum((bill_return_items.rate - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)))* bill_return_items.qty) as net_amt , bill_return_items.vat as vat_percent , sum( (((bill_return_items.rate) - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt))) - ((((bill_return_items.rate) - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(bill_return_items.vat)))) * (bill_return_items.qty) ) as tax_amt')
            ->get();

        }else{
            $arrBillVatSummary = BillReturnItems::join('bill_returns','bill_returns.id', '=','bill_return_items.return_id' )
            ->join('billings','billings.id', '=','bill_returns.billing_id' )
            ->where('bill_return_items.return_id','=',  $return_id)
            ->groupBy('bill_return_items.vat')
            ->selectRaw('sum( (bill_return_items.rate - 
                ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)) ) * bill_return_items.qty) as sale_amt , 
                sum(((bill_return_items.rate - 
                    ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)) ) * bill_return_items.qty) + ( (((bill_return_items.rate - 
                        ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)) ) * bill_return_items.qty) * bill_return_items.vat) /100 )) as net_amt ,
                        bill_return_items.vat as vat_percent , 
                        sum(( (((bill_return_items.rate - 
                            ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)) ) * bill_return_items.qty) * bill_return_items.vat) /100 )) as tax_amt' )
            ->get();

        }
        

        return Response(array(
            "status" => "success",
            "message" => "Bill return successfully",
            "datetime" => Carbon::now()->format('d-m-Y H:i'),
            "getReturnBill" => $itemReturn,
            'arrVatSummary' => $arrBillVatSummary,
        ));

    }catch(Exception $e){
        Log::error("BillingsController@saveReturn ".$e);
        return View("errors.exception");
    }
}

    /**
     * pay the home delivery bill 
     * @param  Request $Request Requested data object 
     * @return return ajax responce 
     */
    public function payHomeDelivery(Request $Request){

        try{
          $req = $Request->all()['data'];

          if($req['billing_status']=='home' && $req['home_delivery'] == 1){

            $billing =  Billing::find($req['id']);



            $billing->fill([

                'received' => $billing['received'] - $billing['balance'],
                'balance' => 0,
                'cash_amt' => $billing['received'] - $billing['balance'],
                'payment_mode' => 1,
            ]);
            $billing->save();

            return Response(array(
                "status" => "success",
                "message" => "Payment received from bill successfully ",
            ));
        }


    }catch(Exception $e){
        Log::error("BillingsController@payHomeDelivery ".$e);
        return View("errors.exception");
    }
}

    /**
     * update stock on return item actions
     * @param  string $operation which type of operation have done 
     * @param  object $item      object of which item stock update
     * @param  int $typeId    id of item action, return table action
     */
    public function updateItemStockOnReturn($operation, $item, $typeId, $intBillingItemId=0){

        try{
            $item_ = Item::find($item["id"]);

            if($operation == "insert"){
                $item_->stock = $item_->stock + $item["qty"];
                $this->updateStock("insert",9, $typeId, $intBillingItemId,  $item_->stock);
            }else if($operation == "delete"){
                $item_->stock = $item_->stock - $item["qty"];
                $this->updateStock("delete",9, $typeId, $intBillingItemId,  $item_->stock);

            }
            $item_->save();

        }catch(Exception $e){
            Log::error("BillingsController@updateItemStockOnReturn ".$e);
            return View("errors.exception");
        }
        
    }



    /**
     * update item stock on billing
     * @param  string $operation type of operation
     * @param  object $item      item object of which item stock has to update
     * @param  object $prevItem  Item object from database before update
     * 
     */
    public function updateItemStock($operation, $item, $prevItem, $typeId, $intBillItemId = 0){

        try{
            $item_ = Item::find($item["id"]);


            $stock_type = 0;

            if($operation == "insert"){

                /** for type of stocks table */
                if($item['itemType'] == 1){
                    $stock_type = 7;
                }else if($item['itemType'] == 2){
                    $stock_type = 8;
                }else{
                    $stock_type = 6;
                }

                $item_->stock = $item_->stock - $item["qty"];
                $this->updateStock("insert",$stock_type, $typeId, $intBillItemId,  $item_->stock);

            }else if($operation == "update"){
                $prev = 0 ;

                /** for type of stocks table */
                if($item['itemType'] == 1){
                    $stock_type = 7;
                }else if($item['itemType'] == 2){
                    $stock_type = 8;
                }else{
                    $stock_type = 6;
                }

                if(!$prevItem->isEmpty()){
                    $prev  = $prevItem->first()->qty;
                }

                $item_->stock = $item_->stock - $item["qty"] + $prev;


                $this->updateStock("delete",$stock_type, $typeId, $prevItem->first()->id,  $item_->stock);
                $this->updateStock("insert",$stock_type, $typeId, $intBillItemId,  $item_->stock);

            }else if($operation == "delete"){
                if($item['itemType'] == 1){
                    $stock_type = 7;
                }else if($item['itemType'] == 2){
                    $stock_type = 8;
                }else{
                    $stock_type = 6;
                }

                $item_->stock = $item_->stock + $item["qty"];
                $this->updateStock("delete",$stock_type, $typeId, $intBillItemId,  $item_->stock);
            }else if($operation == "void"){

                $this->updateStock("void",6, $typeId, $intBillItemId, $item_->stock);
            }

            $item_->save();

        }catch(Exception $e){
            Log::error("BillingsController@updateItemStock ".$e);
            return View("errors.exception");
        }
    }





    public function updateStock($operation, $type, $typeId, $itemId, $itemStock){

        try{
            if($operation=="insert"){
                Stock::insert(array(
                    "item_id" => $itemId,
                    "type" => $type,
                    "type_id" => $typeId,
                    "stock" => $itemStock,
                    "date" => Carbon::now()
                ));
            }else if($operation=="update"){
                Stock::where("type_id", "=", $typeId)
                ->where("item_id", "=", $itemId)
                ->where("type", "=", $type)
                ->update(array("stock" => $itemStock));

            }else if($operation =="delete"){
                $dbItem = Stock::where("type_id", "=", $typeId)
                ->where("item_id", "=", $itemId)
                ->where("type","=",$type)
                ->delete();
            }else if($operation =="void"){
                $item =  Stock::where("type_id", "=", $typeId)
                ->where("item_id", "=", $itemId)
                ->where("type", "=", $type)
                ->update(array("type" => 11));
            }

        }catch(Exception $e){
            Log::error("BillingsController@updateStock ".$e);
            return View("errors.exception");
        }
        
    }



    public function history(Request $Request){


        try{
            $reqPara = $Request->all()["data"];
            $filter =  $reqPara["filter"];
            $customer = $reqPara["customer"];
            $customerHistory = array();
            $customerDetails = Customer::where("mobile","=", $customer)->get();

            $items = Item::with(["units",'categories',"offer.item.units","packageList.items.units"])->orderBy('itemName')->get();


            if(!$customerDetails->isEmpty()){
                $custId = $customerDetails->first()->id;
                $custName = $customerDetails->first()->name;
                $textColor = 'text-green';

                switch ($filter) {
                    case 'all':
                    $customerHistory = BillingItems::join("billings as b", "billing_items.billing_id", "=", "b.id")
                    ->join("items as i", "i.id", "billing_items.item_id")
                    ->select(\DB::raw(" item_id, itemName, sum(qty) as qty"),'offer_type')
                    ->where("b.customer_id", "=", $custId)
                    ->groupBy("item_id")
                    ->groupBy("offer_type")
                    ->orderBy('qty', 'DESC')
                    ->get();
                    break;

                    case 'yearly':
                    $currentDate = Carbon::now();
                    $customerHistory = BillingItems::join("billings as b", "billing_items.billing_id", "=", "b.id")
                    ->join("items as i", "i.id", "billing_items.item_id")
                    ->select(\DB::raw(" item_id, itemName, sum(qty) as qty"),'offer_type')
                    ->where("b.customer_id", "=", $custId)
                    ->whereYear("b.created_at", "=", $currentDate->year)
                    ->groupBy("item_id")
                    ->groupBy("offer_type")
                    ->orderBy('qty', 'DESC')
                    ->get();

                    break;

                    case 'monthly':
                    $currentDate = Carbon::now();
                    $customerHistory = BillingItems::join("billings as b", "billing_items.billing_id", "=", "b.id")
                    ->join("items as i", "i.id", "billing_items.item_id")
                    ->select(\DB::raw(" item_id, itemName, sum(qty) as qty"),'offer_type')
                    ->where("b.customer_id", "=", $custId)
                    ->whereYear("b.created_at", "=", $currentDate->year)
                    ->whereMonth("b.created_at", "=", $currentDate->month)
                    ->groupBy("item_id")
                    ->groupBy("offer_type")
                    ->orderBy('qty', 'DESC')
                    ->get();

                    break;

                    case 'weekly':
                    $fromDate = Carbon::now()->subDay()->startOfWeek()->toDateString(); 
                    $tillDate = Carbon::now()->subDay()->toDateString();
                    $customerHistory = BillingItems::join("billings as b", "billing_items.billing_id", "=", "b.id")
                    ->join("items as i", "i.id", "billing_items.item_id")
                    ->select(DB::raw(" item_id, itemName, sum(qty) as qty"),'offer_type')
                    ->where("b.customer_id", "=", $custId)
                    ->whereBetween( DB::raw('date(b.created_at)'), [$fromDate, $tillDate] )
                    ->groupBy("item_id")
                    ->groupBy("offer_type")
                    ->orderBy('qty', 'DESC')
                    ->get();

                    break;
                }

            }else{
                $custName = 'Customer Not Found';
                $textColor = 'text-red';
            }

            foreach ($customerHistory as $custitem) {
                foreach ($items as $item) {
                    if($custitem->item_id == $item->id){
                        $temp_vat = $item->vat;
                        $custitem =$this->makeBillingItemObject($custitem, $item);
                        $custitem->vat = $temp_vat;
                    }
                }
            }

        /**
         * Applied pramotional price if date is in between pramotion date
         * Applied XY offer if applicable
         * is_promotion and has_xyoffer proprties added to the item  object
         */
        $customerHistory = $this->applyOffers($customerHistory);

        return response(array(
            "customerHistory" => $customerHistory,
            "custName" => $custName,
            "historyClass" => $textColor,
        ));


    }catch(Exception $e){
        Log::error("BillingsController@history ".$e);
        return View("errors.exception");
    }
}

public function reportZData(){

    try{
        $itemList =[];

        $last_date_time = DB::table('z_sales')->orderBy('day_close', 'desc')->first();
        if(!empty($last_date_time)){
            $last_day_off = $last_date_time->day_close;
        }else{
         $last_day_off = Carbon::now()->format('Y-m-d')." 00:00:00"; 
     }

     $billDetails =DB::select("SELECT billings.id,
        DATE_FORMAT(billings.created_at,'".$this->rDateFormatSQL." %h:%i %p')as dateTime,
        billings.bil_amt 
        FROM billings 
        where billings.billing_status <> 'hold' AND billings.created_at BETWEEN '".$last_day_off."' and '".Carbon::now()->format('Y-m-d H:i:s')."'  ORDER BY billings.created_at ASC");
     $openingCash = DB::select("SELECT IFNULL(sum(opening_cashes.opening_amt),0) as openingAmt 
        FROM opening_cashes 
        where opening_cashes.created_at BETWEEN '".$last_day_off."' and '".Carbon::now()->format('Y-m-d H:i:s')."'"); 

     $saleReturn = DB::select("SELECT IFNULL(sum(bill_returns.amount),0)as saleReturn , 
        sum((((billings.dis_amt/billings.subtotal)*100)*bill_returns.amount)/100) as dis_amt 
        FROM bill_returns INNER JOIN billings on bill_returns.billing_id=billings.id 
        where billings.billing_status <> 'hold' AND bill_returns.created_at BETWEEN '".$last_day_off."' and '".Carbon::now()->format('Y-m-d H:i:s')."'"); 
     $sale = DB::select("SELECT IFNULL(sum(billings.cash_amt),0) as cashAmt,
        IFNULL(sum(CASE WHEN billings.payment_mode = 5 THEN 0 ELSE billings.card_amt END),0)as cardAmt,IFNULL(sum(billings.bil_amt),0)as billAmt 
        FROM billings 
        where billings.billing_status <> 'hold' AND billings.created_at BETWEEN '".$last_day_off."' and '".Carbon::now()->format('Y-m-d H:i:s')."'");


     $saleCredit = DB::select("SELECT IFNULL(sum(billings.bil_amt),0)as creditAmt 
        FROM billings 
        where billings.billing_status <> 'hold' AND billings.payment_mode = 3 and billings.created_at BETWEEN '".$last_day_off."' and '".Carbon::now()->format('Y-m-d H:i:s')."'");

     $saleNoonCredit = DB::select("SELECT IFNULL(sum(billings.bil_amt),0)as creditAmt 
        FROM billings 
        where billings.billing_status <> 'hold' AND billings.payment_mode = 5 and billings.created_at BETWEEN '".$last_day_off."' and '".Carbon::now()->format('Y-m-d H:i:s')."'");

     if(sizeof($billDetails)>0){
        foreach ($billDetails as $o) {                            
            array_push($itemList, array(                                
                "dateTime" => $o->dateTime,
                "id" => \Common::fixBill($o->id),
                "bil_amt" =>  \Common::fixIt($o->bil_amt),
            )
        );

        }
    }


    return $res = array(
        "billDetails" => $itemList,
        "openingCash" => \Common::fixIt($openingCash[0]->openingAmt),    
        "saleReturn"    => \Common::fixIt(($saleReturn[0]->saleReturn)),
        "billAmt"   =>\Common::fixIt($sale[0]->billAmt),
        "cashAmt"   => \Common::fixIt($sale[0]->cashAmt),
        "cardAmt"   => \Common::fixIt($sale[0]->cardAmt),
        "saleCredit"    => \Common::fixIt($saleCredit[0]->creditAmt),
        "saleNoonCredit"    => \Common::fixIt($saleNoonCredit[0]->creditAmt),
        "last_day_off"  => (new Carbon($last_day_off))->format($this->rDateFormat.' h:i A'),
        "ztoDatePrint"  => Carbon::now()->format($this->rDateFormat.' h:i A'),
    );

}catch(Exception $e){
    Log::error("BillingsController@reportZData ".$e);
    return View("errors.exception");
}
} 

public function reportZQtyData(){

    try{
        $itemList = array();

        $last_date_time = DB::table('z_sales')->orderBy('day_close', 'desc')->first();
        if(!empty($last_date_time)){
            $last_day_off = $last_date_time->day_close;
        }else{
          $last_day_off = Carbon::now()->format('Y-m-d')." 00:00:00"; 
      }

      $billDetails =DB::select("SELECT sum(bi.qty) as qty, i.itemName
    /*(SELECT items.itemName FROM items WHERE items.id=billing_items.item_id)as itemName 
    FROM billing_items as bi INNER JOIN billings on billing_items.billing_id=billings.id */
    FROM (billings AS b  INNER JOIN billing_items AS bi ON bi.billing_id=b.id) INNER JOIN items i ON bi.item_id = i.id
    where b.billing_status <> 'hold' AND b.created_at BETWEEN '".$last_day_off."' and '".Carbon::now()->format('Y-m-d H:i:s')."' GROUP BY bi.item_id");
      if(sizeof($billDetails)>0){
        foreach ($billDetails as $o) {
            array_push($itemList, array(                    
                "itemName" => $o->itemName,
                "qty" => \Common::fixQty($o->qty),                  
            )
        );
        }
    }
    return $res = array(
        "billDetails" => $itemList,
        "last_day_off"  => (new Carbon($last_day_off))->format($this->rDateFormat.' h:i A'),
        "ztoDatePrint"  => Carbon::now()->format($this->rDateFormat.' h:i A'),
    );

}catch(Exception $e){
    Log::error("BillingsController@reportZQtyData ".$e);
    return View("errors.exception");
}
}

public function getLedgerXSales(Request $Request){  

    try{    
        $dateArray = $Request->all()["data"]; 
        $fromDate = $dateArray['fromDate'];
        $toDate = $dateArray['toDate'];    
        $itemList = []; 

        $billDetails =DB::select("SELECT billings.id,
            DATE_FORMAT(billings.created_at,'".$this->rDateFormatSQL." %h:%i %p')as dateTime,
            billings.bil_amt 
            FROM billings 
            where billings.billing_status <> 'hold' AND billings.created_at BETWEEN '".$fromDate."' and '".$toDate."'  ORDER BY billings.created_at ASC");
        $openingCash = DB::select("SELECT IFNULL(sum(opening_cashes.opening_amt),0) as openingAmt 
            FROM opening_cashes
            where opening_cashes.created_at BETWEEN '".$fromDate."' and '".$toDate."'"); 
        $saleReturn = DB::select("SELECT IFNULL(sum(bill_returns.amount),0)as saleReturn , 
            sum((((billings.dis_amt/billings.subtotal)*100)*bill_returns.amount)/100) as dis_amt
            FROM bill_returns INNER JOIN billings on bill_returns.billing_id=billings.id
            where billings.billing_status <> 'hold' AND bill_returns.created_at BETWEEN '".$fromDate."' and '".$toDate."'"); 
        $sale = DB::select("SELECT IFNULL(sum(billings.cash_amt),0) as cashAmt,
            IFNULL(sum(CASE WHEN billings.payment_mode = 5 THEN 0 ELSE billings.card_amt END),0)as cardAmt,IFNULL(sum(billings.bil_amt),0)as billAmt 
            FROM billings 
            where billings.billing_status <> 'hold' AND billings.created_at BETWEEN '".$fromDate."' and '".$toDate."'");


        $saleCredit = DB::select("SELECT IFNULL(sum(billings.bil_amt),0)as creditAmt 
            FROM billings 
            where billings.billing_status <> 'hold' AND billings.payment_mode = 3 and billings.created_at BETWEEN '".$fromDate."' and '".$toDate."'");

        $saleNoonCredit = DB::select("SELECT IFNULL(sum(billings.bil_amt),0)as creditAmt 
            FROM billings 
            where billings.billing_status <> 'hold' AND billings.payment_mode = 5 and billings.created_at BETWEEN '".$fromDate."' and '".$toDate."'");

        if(sizeof($billDetails)>0){
            foreach ($billDetails as $o) {                            
                array_push($itemList, array(                                
                    "dateTime" => $o->dateTime,
                    "id" => \Common::fixBill($o->id),
                    "bil_amt" =>  \Common::fixIt($o->bil_amt),
                )
            );

            }
        }

        $labels = $this->getLabels();

        $res = array(
            "billDetails" => $itemList,
            "openingCash" => \Common::fixIt($openingCash[0]->openingAmt),    
            "saleReturn"    => \Common::fixIt(($saleReturn[0]->saleReturn)),
            "billAmt"   =>\Common::fixIt($sale[0]->billAmt ),
            "cashAmt"   => \Common::fixIt($sale[0]->cashAmt),
            "cardAmt"   => \Common::fixIt($sale[0]->cardAmt),
            "saleCredit"    => \Common::fixIt($saleCredit[0]->creditAmt),
            "saleNoonCredit"    => \Common::fixIt($saleNoonCredit[0]->creditAmt),
            "labels"    => $labels,
            "xtoDatePrint" => (new Carbon($toDate))->format($this->rDateFormat.' h:i A'),
            "xfromDatePrint" => (new Carbon($fromDate))->format($this->rDateFormat.' h:i A'), 
        );

        if($Request->ajax()){
            return Response($res,200);
        }else{
            return View("pages.billing.billing",$res);
        }

    }catch(Exception $e){
        Log::error("BillingsController@getLedgerXSales ".$e);
        return View("errors.exception");
    }
}

public function mallDetails(){

    try{
        $MallAddress ="NA";
        $MallName ="NA";
        $MAllLogo ="NA";
        $address = DB::select("SELECT options.value as address FROM options where options.type = 'general' and options.key = 'register_address'");
        $mallName = DB::select("SELECT options.value as mallName FROM options where options.type = 'general' and options.key = 'mall_name'");
        $logo = DB::select("SELECT options.value as logo FROM options where options.type = 'general' and options.key = 'logo'");
        $telephone = DB::select("SELECT options.value as telephone FROM options where options.type = 'general' and options.key = 'mall_telephone'");
        $tnc_eng = DB::select("SELECT options.value as tnc_eng FROM options where options.type = 'general' and options.key = 'tnc_eng'");
        $tnc_ar = DB::select("SELECT options.value as tnc_ar FROM options where options.type = 'general' and options.key = 'tnc_ar'");
        $footer = DB::select("SELECT options.value as footer FROM options where options.type = 'general' and options.key = 'footer'");
        $mall_website = DB::select("SELECT options.value as mall_website FROM options where options.type = 'general' and options.key = 'mall_website'");
        $enable_sms_billing = DB::select("SELECT options.value as enable_sms_billing FROM options where options.type = 'general' and options.key = 'enable_sms_billing'");
        $open_cash_drawer = DB::select("SELECT options.value as open_cash_drawer FROM options where options.type = 'billing' and options.key = 'open_cash_drawer'");
        $loyalty = DB::select("SELECT options.value as loyalty FROM options where options.type = 'billing' and options.key = 'loyalty'");


        return $res = array(      
           "address"  => (isset($address[0]->address)) ? $address[0]->address : '',   
           "mallName"  => (isset($mallName[0]->mallName)) ? $mallName[0]->mallName : '', 
           "logo"  => (isset($logo[0]->logo)) ? $logo[0]->logo : '',  
           "telephone"  => (isset($telephone[0]->telephone)) ? $telephone[0]->telephone : '',  
           "tnc_eng"  => (isset($tnc_eng[0]->tnc_eng)) ? $tnc_eng[0]->tnc_eng : '',  
           "tnc_ar"  => (isset($tnc_ar[0]->tnc_ar)) ? $tnc_ar[0]->tnc_ar : '', 
           "footer"  => (isset($footer[0]->footer)) ? $footer[0]->footer : '', 
           "mall_website"  => (isset($mall_website[0]->mall_website)) ? $mall_website[0]->mall_website : '',   
           "enable_sms_billing"  =>  (isset($enable_sms_billing[0]->enable_sms_billing)) ? $enable_sms_billing[0]->enable_sms_billing : '',  
           "open_cash_drawer"  =>  (isset($open_cash_drawer[0]->open_cash_drawer)) ? $open_cash_drawer[0]->open_cash_drawer : '',  
           "loyalty"  => (isset($loyalty[0]->loyalty)) ? $loyalty[0]->loyalty : 0,           
       );

    }catch(Exception $e){
        Log::error("BillingsController@mallDetails ".$e);
        return View("errors.exception");
    }
} 

public function verify(Request $Request){
    try{   
        
        $users = DB::select("select * from `users` inner join roles on roles.id = users.roleId where BINARY roles.role = 'SALES SUPERVISOR' or BINARY roles.role = 'Sales Supervisor' or BINARY roles.role = 'sales supervisor' or users.roleId=1 or  users.roleId=2");

        foreach ($users as $row) {
            if(Hash::check(trim($Request->data), $row->password)){
                return array('matched' =>  "true");
            }
        }



        // $users = User::where("roleId", "=", 2)->orWhere("roleId", "=", 1)->get();
        // foreach ($users as $row) {
        //     if(Hash::check(trim($Request->data), $row->password)){
        //         return array('matched' =>  "true");
        //     }
        // }
        return array('matched' =>  "false");    

    }catch(Exception $e){
        Log::error("BillingsController@verify ".$e);
        return View("errors.exception");
    }    
}


public function eod(Request $Request){   
    try{      
        $insert_eod = zSales::insertGetId(array(
            "day_close" => Carbon::now()->format('Y-m-d H:i:s'),
            'created_at'    => Carbon::now()->format('Y-m-d H:i:s'),
        ));  
        return array('status' =>  "success");   

    }catch(Exception $e){
        Log::error("BillingsController@eod ".$e);
        return View("errors.exception");
    }       
}


public function getItemByBarcode(Request $Request){

    try{
        $barode = $Request->all()['data'];
        $item = Item::with(["units",'categories',"offer.item.units","packageList.items.units"])->where('barcode', '=',$barode)->get();
        $item = $this->applyOffers($item);

        return Response(array(
            "item" => $item,
        ),200);

    }catch(Exception $e){
        Log::error("BillingsController@getItemByBarcode ".$e);
        return View("errors.exception");
    }  
}

public function getItemByBarcodeWithSerial(Request $Request){

    try{
        $req = $Request->all()['data'];

        /** check serial number is present in billing items or not  */
        $serial_duplicate_check = BillingItems::join('items', 'billing_items.item_id' , '=' , 'items.id')
        ->where('items.barcode','=', $req['barcode'] )
        -> where('serial_no','=', $req['serial_no'] )->get();

        if(count($serial_duplicate_check)){
            return Response(array(
                "status" => 'warning',
                "serial_no" => $req['serial_no'],
            ),200);
        }else{
            $item = Item::with(["units",'categories',"offer.item.units","packageList.items.units"])->where('barcode', '=',  $req['barcode'] )->get();
            $item = $this->applyOffers($item);

            return Response(array(
                "status" => 'success',
                "item" => $item,
                "serial_no" => $req['serial_no'],
            ),200);
        }

    }catch(Exception $e){
        Log::error("BillingsController@getItemByBarcodeWithSerial ".$e);
        return View("errors.exception");
    } 

}

public function getItemBySearchAll(Request $Request){

    try{
        $searchKey = $Request->all()['data'];
        $item = Item::with(["units",'categories',"offer.item.units","packageList.items.units"])
        ->join('categories', 'items.category', '=', 'categories.id')
        ->select('items.*', 'categories.categorie')
        ->orWhere('categories.categorie','LIKE','%'.$searchKey.'%')
        ->orWhere('barcode', 'LIKE', '%'.$searchKey.'%')
        ->orWhere('itemName', 'LIKE',  '%'.$searchKey.'%')
        ->orderBy('id', 'asc')
        ->take(10)
        ->get();

        $item = $this->applyOffers($item);

        return Response(array(
            'items' => $item,
        ),200);

    }catch(Exception $e){
        Log::error("BillingsController@getItemBySearchAll ".$e);
        return View("errors.exception");
    } 
}



public function getExistingBill(Request $Request){


    try{
        if (Auth::check())
            {
                $userId = \Auth::id();
            }
            $billId =  $Request->all()['data'];



            if($this->getAutAccess($Request) == 2){
                $itemReturn = BillReturns::with(['rItem','bill'])->where('bill_returns.billing_id','=', $billId)->get();
                $bill = Billing::with(['itemList','homeDeliveryCustomer','_customer.countryCode', "itemList.item.units",'itemList.item.categories',"itemList.item.offer.item.units","itemList.item.packageList.items.units","taxRefund"])
                ->where("billing_status", "not like", "hold")
                ->where('id','=', $billId)->first();

        /*$arrBillVatSummary = BillingItems::where('billing_id','=', $billId)
        ->groupBy('vat')
        ->selectRaw('sum( ((rate*100)/(100+vat)) * qty) as sale_amt , sum(rate * qty) as net_amt , vat as vat_percent , sum((rate - ((rate * 100)/(100 + vat))) * qty) as tax_amt' )
        ->get();*/


        if($this->intVatType !=2){
            $arrBillVatSummary = BillingItems::join('billings','billings.id', '=','billing_items.billing_id' )
            ->where('billing_items.billing_id','=', $billId)
            ->groupBy('billing_items.vat')
            ->selectRaw('sum( (billing_items.rate - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) - (((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt))) - ((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(billing_items.vat)))) ) * billing_items.qty) as sale_amt , sum((billing_items.rate - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))* billing_items.qty) as net_amt , billing_items.vat as vat_percent , sum( (((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt))) - ((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(billing_items.vat)))) * (billing_items.qty) ) as tax_amt' )
            ->get();

        }else{
            $arrBillVatSummary = BillingItems::join('billings','billings.id', '=','billing_items.billing_id' )
            ->where('billing_items.billing_id','=', $billId)
            ->groupBy('billing_items.vat')
            ->selectRaw('sum( (billing_items.rate - 
                ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) as sale_amt , 
                sum(((billing_items.rate - 
                    ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) + ( (((billing_items.rate - 
                        ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) * billing_items.vat) /100 )) as net_amt ,
                        billing_items.vat as vat_percent , 
                        sum(( (((billing_items.rate - 
                            ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) * billing_items.vat) /100 )) as tax_amt' )
            ->get();
        }
        


    }elseif($this->getAutAccess($Request) == 1){
        $itemReturn = BillReturns::with(['rItem','bill', "rItem.items.units",'rItem.items.categories',"rItem.items.offer.item.units","rItem.items.packageList.items.units",'bill._customer'])
        ->join('billings','billings.id', '=','bill_returns.billing_id' )
        ->where( 'billings.user_id', '=', $userId )
        ->where('bill_returns.billing_id','=', $billId)
        ->select(['bill_returns.*', 'bill_returns.id as retid'])
        ->get();

        $bill = Billing::with(['itemList','homeDeliveryCustomer','_customer.countryCode', "itemList.item.units",'itemList.item.categories',"itemList.item.offer.item.units","itemList.item.packageList.items.units","taxRefund"])
        ->where("billing_status", "not like", "hold")
        ->where('id','=', $billId)
        ->where('user_id', '=', $userId )->first();


        if($this->intVatType !=2){
            $arrBillVatSummary = BillingItems::join('billings','billings.id', '=','billing_items.billing_id' )
            ->where('billing_items.billing_id','=', $billId)
            ->where('billings.user_id', '=', $userId )
            ->groupBy('billing_items.vat')
            ->selectRaw('sum( (billing_items.rate - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) - (((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt))) - ((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(billing_items.vat)))) ) * billing_items.qty) as sale_amt , 
                sum((billing_items.rate - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))* billing_items.qty) as net_amt ,
                billing_items.vat as vat_percent , 
                sum( (((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt))) - ((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(billing_items.vat)))) * (billing_items.qty) ) as tax_amt' )
            ->get();
        }else{
            $arrBillVatSummary = BillingItems::join('billings','billings.id', '=','billing_items.billing_id' )
            ->where('billing_items.billing_id','=', $billId)
            ->where('billings.user_id', '=', $userId )
            ->groupBy('billing_items.vat')
            ->selectRaw('sum( (billing_items.rate - 
                ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) as sale_amt , 
                sum(((billing_items.rate - 
                    ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) + ( (((billing_items.rate - 
                        ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) * billing_items.vat) /100 )) as net_amt ,
                        billing_items.vat as vat_percent , 
                        sum(( (((billing_items.rate - 
                            ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) * billing_items.vat) /100 )) as tax_amt' )
            ->get();

        }


    }else{
        $itemReturn =[];
        $bill =[]; 
        $arrBillVatSummary = [];
    }


    if(count($itemReturn)){


        /** create item object as per requirment in return bill object  */
        foreach ($itemReturn as $returnI) {
            /** Added dist_amt for calculation of totalAmount in vuejs */
            $returnI = $this->addCustomerDetails($returnI);
            $returnI['dis_amt']=0;
            foreach ($returnI->rItem as $rItem) {
                $rItem =$this->makeBillingItemObject($rItem, $rItem->items);
            }
        }
    }

    if(!empty($bill)){


        $bill->itemList = $this->applyOffers($bill->itemList);


        $bill = $this->addCustomerDetails($bill);
        $bill = $this->getQtyBillToReturn($bill,$itemReturn);

        $bill->billdate =  Carbon::createFromFormat('Y-m-d H:i:s', $bill->created_at)->format('d M Y');
        $bill->billdatetime =  Carbon::createFromFormat('Y-m-d H:i:s', $bill->created_at)->format($this->rDateFormat.' H:i');


        foreach ($bill->itemList as $bilItems) {
            $arrItem= array();
            $arrItem[0] = $bilItems->item;
            $arrItem = $this->applyOffers($arrItem);

            $bilItems =$this->makeBillingItemObject($bilItems, $arrItem[0]);
        }

    }

    return Response(array(
        'getBill' => $bill,
        'getreturnBill' => $itemReturn,
        'arrVatSummary' => $arrBillVatSummary,
    ),200);

}catch(Exception $e){
    Log::error("BillingsController@getExistingBill ".$e);
    return View("errors.exception");
} 
}

public function getReturnBill(Request $Request){


    try{
        if (Auth::check())
            {
                $userId = \Auth::id();
            }
            $returnId =  $Request->all()['data'];


            if($this->getAutAccess($Request) == 2){
                $itemReturn = BillReturns::with(['rItem','bill', "rItem.items.units",'rItem.items.categories',"rItem.items.offer.item.units","rItem.items.packageList.items.units",'bill._customer'])
                ->where('bill_returns.id','=', $returnId)
                ->select(['bill_returns.*', 'bill_returns.id as retid'])
                ->first();

        /*$arrBillVatSummary = BillReturnItems::where('return_id','=', $returnId)
        ->groupBy('vat')
        ->selectRaw('sum(((rate*100)/(100+vat)) * qty) as sale_amt , sum(rate * qty) as net_amt , vat as vat_percent , ((sum(((rate) - ((rate * 100)/(100 + vat))) * (qty)))) as tax_amt' )
        ->get();*/



        if($this->intVatType != 2){
            $arrBillVatSummary = BillReturnItems::join('bill_returns','bill_returns.id', '=','bill_return_items.return_id' )
            ->join('billings','billings.id', '=','bill_returns.billing_id' )
            ->where('bill_return_items.return_id','=',  $returnId)
            ->groupBy('bill_return_items.vat')
            ->selectRaw('sum( (bill_return_items.rate - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)) - (((bill_return_items.rate) - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt))) - ((((bill_return_items.rate) - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(bill_return_items.vat)))) ) * bill_return_items.qty) as sale_amt , sum((bill_return_items.rate - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)))* bill_return_items.qty) as net_amt , bill_return_items.vat as vat_percent , sum( (((bill_return_items.rate) - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt))) - ((((bill_return_items.rate) - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(bill_return_items.vat)))) * (bill_return_items.qty) ) as tax_amt')
            ->get();

        }else{
            $arrBillVatSummary = BillReturnItems::join('bill_returns','bill_returns.id', '=','bill_return_items.return_id' )
            ->join('billings','billings.id', '=','bill_returns.billing_id' )
            ->where('bill_return_items.return_id','=',  $returnId)
            ->groupBy('bill_return_items.vat')
            ->selectRaw('sum( (bill_return_items.rate - 
                ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)) ) * bill_return_items.qty) as sale_amt , 
                sum(((bill_return_items.rate - 
                    ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)) ) * bill_return_items.qty) + ( (((bill_return_items.rate - 
                        ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)) ) * bill_return_items.qty) * bill_return_items.vat) /100 )) as net_amt ,
                        bill_return_items.vat as vat_percent , 
                        sum(( (((bill_return_items.rate - 
                            ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)) ) * bill_return_items.qty) * bill_return_items.vat) /100 )) as tax_amt' )
            ->get();

            
        }
        

    }else if($this->getAutAccess($Request) == 1){
        $itemReturn = BillReturns::with(['rItem','bill', "rItem.items.units",'rItem.items.categories',"rItem.items.offer.item.units","rItem.items.packageList.items.units",'bill._customer','bill._customer'])
        ->join('billings','billings.id', '=','bill_returns.billing_id' )
        ->where( 'billings.user_id', '=', $userId )
        ->where('bill_returns.id','=', $returnId)
        ->select(['bill_returns.*', 'bill_returns.id as retid'])
        ->first();


        if($this->intVatType != 2){
            $arrBillVatSummary = BillReturnItems::join('bill_returns','bill_returns.id', '=','bill_return_items.return_id' )
            ->join('billings','billings.id', '=','bill_returns.billing_id' )
            ->where('bill_return_items.return_id','=',  $returnId)
            ->where('billings.user_id', '=', $userId )
            ->groupBy('bill_return_items.vat')
            ->selectRaw('sum( (bill_return_items.rate - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)) - (((bill_return_items.rate) - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt))) - ((((bill_return_items.rate) - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(bill_return_items.vat)))) ) * bill_return_items.qty) as sale_amt , sum((bill_return_items.rate - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)))* bill_return_items.qty) as net_amt , bill_return_items.vat as vat_percent , sum( (((bill_return_items.rate) - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt))) - ((((bill_return_items.rate) - ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(bill_return_items.vat)))) * (bill_return_items.qty) ) as tax_amt' )
            ->get();
        }else{

            $arrBillVatSummary = BillReturnItems::join('bill_returns','bill_returns.id', '=','bill_return_items.return_id' )
            ->join('billings','billings.id', '=','bill_returns.billing_id' )
            ->where('bill_return_items.return_id','=',  $returnId)
            ->where('billings.user_id', '=', $userId )
            ->groupBy('bill_return_items.vat')
            ->selectRaw('sum( (bill_return_items.rate - 
                ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)) ) * bill_return_items.qty) as sale_amt , 
                sum(((bill_return_items.rate - 
                    ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)) ) * bill_return_items.qty) + ( (((bill_return_items.rate - 
                        ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)) ) * bill_return_items.qty) * bill_return_items.vat) /100 )) as net_amt ,
                        bill_return_items.vat as vat_percent , 
                        sum(( (((bill_return_items.rate - 
                            ((billings.dis_amt * bill_return_items.rate)/(billings.subtotal + billings.v_amt)) ) * bill_return_items.qty) * bill_return_items.vat) /100 )) as tax_amt' )
            ->get();

        }

    }else{
      $itemReturn = [];
      $arrBillVatSummary=[];
  }


  if(count($itemReturn)){


    /** Added dist_amt for calculation of totalAmount in vuejs */
    $itemReturn = $this->addCustomerDetails($itemReturn);
    $itemReturn['dis_amt']=0;
    $itemReturn['v_amt']=0;
    $itemReturn['loyalty_discount']=0;
    $itemReturn['loyalty_points']=0;
    $itemReturn->billdatetime =  Carbon::createFromFormat('Y-m-d H:i:s', $itemReturn->created_at)->format($this->rDateFormat.' H:i');
    foreach ($itemReturn->rItem as $rItem) {
        $rItem =$this->makeBillingItemObject($rItem, $rItem->items);
    }
}

return Response(array(
    'getreturnBill' => $itemReturn,
    'arrVatSummary' => $arrBillVatSummary,
),200);


}catch(Exception $e){
    Log::error("BillingsController@getReturnBill ".$e);
    return View("errors.exception");
} 
}

function getHomeBill(Request $Request){

    try{
        if (Auth::check())
            {
                $userId = \Auth::id();
            }
            $billId =  $Request->all()['data'];


            if($this->getAutAccess($Request) == 2){
                $itemReturn = BillReturns::with(['rItem','bill'])->where('bill_returns.billing_id','=', $billId)->get();
                $bill = Billing::with(['itemList','homeDeliveryCustomer','_customer', "itemList.item.units",'itemList.item.categories',"itemList.item.offer.item.units","itemList.item.packageList.items.units"])
                ->where('id','=', $billId)->first();
            }else if($this->getAutAccess($Request) == 1){
                $itemReturn = BillReturns::with(['rItem','bill'])
                ->join('billings','billings.id', '=','bill_returns.billing_id' )
                ->where( 'billings.user_id', '=', $userId )
                ->where('bill_returns.billing_id','=', $billId)
                ->select(['bill_returns.*', 'bill_returns.id as retid'])
                ->get();
                $bill = Billing::with(['itemList','homeDeliveryCustomer','_customer', "itemList.item.units",'itemList.item.categories',"itemList.item.offer.item.units","itemList.item.packageList.items.units"])
                ->where('id','=', $billId)
                ->where('user_id', '=', $userId )->first();
            }else{
                $itemReturn = [];
                $bill = [];
            }


            if(count($itemReturn)){

                $items = Item::with(["units",'categories',"offer.item.units","packageList.items.units"])->get();

                $items = $this->applyOffers($items);

                /** create item object as per requirment in return bill object  */
                foreach ($itemReturn as $returnI) {
                    /** Added dist_amt for calculation of totalAmount in vuejs */
                    $returnI = $this->addCustomerDetails($returnI);
                    $returnI['dis_amt']=0;
                    foreach ($returnI->rItem as $rItem) {
                        foreach ($items as $item) {
                            if($rItem->item_id == $item->id){
                                $rItem =$this->makeBillingItemObject($rItem, $item);
                            }
                        }
                    }
                }
            }

            if(!empty($bill)){

                $items = Item::with(["units",'categories',"offer.item.units","packageList.items.units"])->get();
                $items = $this->applyOffers($items);

                $bill->itemList = $this->applyOffers($bill->itemList);

                $bill = $this->addCustomerDetails($bill);
                $bill = $this->getQtyBillToReturn($bill,$itemReturn);

                foreach ($bill->itemList as $bilItems) {
                    foreach ($items as $item) {
                        if($bilItems->item_id == $item->id){
                            $bilItems =$this->makeBillingItemObject($bilItems, $item);
                        }
                    }
                }

            }

            return Response(array(
                'getBill' => $bill,
                'getreturnBill' => $itemReturn,
            ),200);

        }catch(Exception $e){
            Log::error("BillingsController@getHomeBill ".$e);
            return View("errors.exception");
        } 
    }


    function searchBill(Request $Request){
        try{
            $reqData =$Request->all();
            $resultdb = Billing::select("id")->where("id","=",$reqData["term"])->get();
            $resultArray = [];
            foreach ($resultdb as $r ) {
                array_push($resultArray, $r->id ."");
            }

            return Response($resultArray);

        }catch(Exception $e){
            Log::error("BillingsController@searchBill ".$e);
            return View("errors.exception");
        } 
    }

/** get hold bill is new function same except hold bill status check at the time of get bill,
 * get existing bill function return all type bill except hold bill 
 *  and this function return only hold bill items 
 *  omkesh-6-6-2017
 */
public function getHoldBill(Request $Request){


    try{
        if (Auth::check())
            {
                $userId = \Auth::id();
            }
            $billId =  $Request->all()['data'];


            if($this->getAutAccess($Request) == 2){
                $itemReturn = BillReturns::with(['rItem','bill'])
                ->where('bill_returns.billing_id','=', $billId)->get();
                $bill = Billing::with(['itemList','homeDeliveryCustomer','_customer', "itemList.item.units",'itemList.item.categories',"itemList.item.offer.item.units","itemList.item.packageList.items.units"])
                ->where('id','=', $billId)->first();
            }else if($this->getAutAccess($Request) == 1){
                $itemReturn = BillReturns::with(['rItem','bill', "rItem.items.units",'rItem.items.categories',"rItem.items.offer.item.units","rItem.items.packageList.items.units"])
                ->join('billings','billings.id', '=','bill_returns.billing_id' )
                ->where( 'billings.user_id', '=', $userId )
                ->where('bill_returns.billing_id','=', $billId)
                ->select(['bill_returns.*', 'bill_returns.id as retid'])
                ->get();
                $bill = Billing::with(['itemList','homeDeliveryCustomer','_customer', "itemList.item.units",'itemList.item.categories',"itemList.item.offer.item.units","itemList.item.packageList.items.units"])
                ->where('id','=', $billId)
                ->where('user_id', '=', $userId )->first();
            }else{
             $itemReturn = [];
             $bill = []; 
         }


         if(count($itemReturn)){




            /** create item object as per requirment in return bill object  */
            foreach ($itemReturn as $returnI) {
                /** Added dist_amt for calculation of totalAmount in vuejs */
                $returnI = $this->addCustomerDetails($returnI);
                $returnI['dis_amt']=0;
                foreach ($returnI->rItem as $rItem) {
                    $rItem =$this->makeBillingItemObject($rItem, $rItem->items);
                }
            }
        }

        if(!empty($bill)){


            $bill->itemList = $this->applyOffers($bill->itemList);


            $bill = $this->addCustomerDetails($bill);
            $bill = $this->getQtyBillToReturn($bill,$itemReturn);

            foreach ($bill->itemList as $bilItems) {
                $arrItem= array();
                $arrItem[0] = $bilItems->item;
                $arrItem = $this->applyOffers($arrItem);

                $bilItems =$this->makeBillingItemObject($bilItems, $arrItem[0]);
            }

        }

        return Response(array(
            'getBill' => $bill,
            'getreturnBill' => $itemReturn,
        ),200);

    }catch(Exception $e){
        Log::error("BillingsController@getHoldBill ".$e);
        return View("errors.exception");
    } 

}

/** check given bill id have return bill or not */
public function checkBillReturnExist(Request $Request){

    try{
        $billId =  $Request->all()['data'];



        $check = Billing::leftJoin('bill_returns as br', 'billings.id','br.billing_id')
        ->Where('billings.id','=',$billId)
        ->where(function ($query){
            $query->orWhere('billings.payment_mode','=', 3)
            ->orWhere('billings.balance','<', 0)
            ->orWhere('br.id','<>', null);
        })->get();


        if(count($check)){
            return Response(array(
                'status' => 'yes',
                'billId' => $billId,
                'check' => $check ,
            ),200);
        }else{
            return Response(array(
                'status' => 'no',
                'billId' => $billId,
                'check' => $check ,
            ),200);
        }

    }catch(Exception $e){
        Log::error("BillingsController@checkBillReturnExist ".$e);
        return View("errors.exception");
    } 

}

public function checkAuthDelete($data){
    try{
        $users = User::where("roleId", "=", 2)->orWhere("roleId", "=", 1)->get();
        foreach ($users as $row) {
            if(Hash::check(trim($data), $row->password)){
                return true;
            }
        }
        return false;

    }catch(Exception $e){
        Log::error("BillingsController@checkAuthDelete ".$e);
        return View("errors.exception");
    } 
}

public function getDeletedBills(Request $Request){

    try{
        $strSearchKey = $Request->input('searchkey');

        $arrDeletedBills = BillingDelete::with("_user") 
        ->orWhere("bill_id", "=", "$strSearchKey")
        ->orWhere("taxRefundTagNumber", "=", "$strSearchKey")
        ->orWhere("amount", "=", "$strSearchKey")
        ->orWhere("remark", "like", "%$strSearchKey%")
        ->paginate(25);

        return View('pages.reports.bill_delete.deleted_bills',[
            "arrDeletedBills" => $arrDeletedBills, 
            'searchkey' => $Request->input('searchkey'),
            'date_format'   => $this->rDateFormat .' h:i',
        ]);

    }catch(Exception $e){
        Log::error("BillingsController@getDeletedBills ".$e);
        return View("errors.exception");
    } 
}

public function exportTo(Request $Request){

    try{
      $strSearchKey = $Request->input('searchkey');

      $arrDeletedBills = BillingDelete::with("_user") 
      ->orWhere("bill_id", "=", "$strSearchKey")
      ->orWhere("taxRefundTagNumber", "=", "$strSearchKey")
      ->orWhere("amount", "=", "$strSearchKey")
      ->orWhere("remark", "like", "%$strSearchKey%")
      ->get();

      if($Request->input("pdf")){
       $pdf = PDF::loadView('pages.reports.bill_delete.deleted_bills_pdf', [
        'arrDeletedBills' => $arrDeletedBills,
        'i' => 1,
        'date_format'   => $this->rDateFormat .' h:i',

    ]); 

       $pdf->setPaper('a4');
       $carbon = Carbon::today();
       $timestamp = $carbon->timestamp;
       $format = $carbon->format($this->rDateFormat);
       $exportName = 'DeletedBills-'.$format.'.pdf';
       return $pdf->download($exportName);

   }else if(Input::get('xls')){
       if(sizeof($arrDeletedBills) >0){
           $carbon = Carbon::today();
           $timestamp = $carbon->timestamp;
           $format = $carbon->format($this->rDateFormat);
           $exportName = 'DeletedBills-'.$format; 
           \App\Legacy\LegacyExcel::create($exportName, function ($excel) use($arrDeletedBills){

            $excel->sheet('DeletedBills', function ($sheet) use($arrDeletedBills){

                $dbList = array();
                $i=1;
                foreach ($arrDeletedBills as $o) {              

                  array_push($dbList, array(
                    '#'  => $i++,
                    trans('billing.bill_no')  => "".Common::fixBill($o->bill_id),
                    trans('glob.taxRefundTagNumber')  => $o->taxRefundTagNumber, 
                    trans('billing.bill_amount')  => "".Common::fixIt($o->amount),
                    trans('glob.deleted_by')  => (isset($o->_user->email) ? $o->_user->email : '' ),                                   
                    trans('glob.remark')  => $o->remark,                      
                    trans('glob.datetime')  => $o->created_at->format($this->rDateFormat.' H:i'),                      

                )
              );
              }                           

              /* setting column names for data - you can of course set it manually*/
              $sheet->appendRow(array_keys($dbList[0])); /* column names*/

              /* getting last row number (the one we already filled and setting it to bold*/
                  $sheet->row($sheet->getHighestRow(), function ($row) {
                    $row->setFontWeight('bold');
                });

                  /* putting users data as next rows*/
                  foreach ($dbList as $l) {
                    $sheet->appendRow($l);
                }
            });

        })->export('xlsx');


           return back();

       }else{
        toastr::warning('Warning', trans("validation.export_error"));
        return back();
    }
}

}catch(Exception $e){
    Log::error("BillingsController@exportTo ".$e);
    return View("errors.exception");
} 
}

/* Function for Activate Billing  get last bill record*/
public function activateBilling(){

    try{

        $bill = Billing::with(['itemList','homeDeliveryCustomer','_customer', "itemList.item.units",'itemList.item.categories',"itemList.item.offer.item.units","itemList.item.packageList.items.units"])
        ->where("billing_status", "not like", "hold")
        ->orderBy('id', 'desc')->first();

    /*$arrBillVatSummary = BillingItems::groupBy('vat')
    ->selectRaw('sum( ((rate*100)/(100+vat)) * qty) as sale_amt , sum(rate * qty) as net_amt , vat as vat_percent , sum((rate - ((rate * 100)/(100 + vat))) * qty) as tax_amt' )
    ->first();*/


    if($this->intVatType !=2){
        $arrBillVatSummary = BillingItems::join('billings','billings.id', '=','billing_items.billing_id' )
        ->where('billing_items.billing_id','=', $billId)
        ->groupBy('billing_items.vat')
        ->selectRaw('sum( (billing_items.rate - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) - (((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt))) - ((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(billing_items.vat)))) ) * billing_items.qty) as sale_amt , sum((billing_items.rate - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))* billing_items.qty) as net_amt , billing_items.vat as vat_percent , sum( (((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt))) - ((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(billing_items.vat)))) * (billing_items.qty) ) as tax_amt' )
        ->get();

    }else{
        $arrBillVatSummary = BillingItems::join('billings','billings.id', '=','billing_items.billing_id' )
        ->where('billing_items.billing_id','=', $billId)
        ->groupBy('billing_items.vat')
        ->selectRaw('sum( (billing_items.rate - 
            ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) as sale_amt , 
            sum(((billing_items.rate - 
                ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) + ( (((billing_items.rate - 
                    ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) * billing_items.vat) /100 )) as net_amt ,
                    billing_items.vat as vat_percent , 
                    sum(( (((billing_items.rate - 
                        ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) * billing_items.vat) /100 )) as tax_amt' )
        ->get();

    }



    if(!empty($bill)){       

        $bill->itemList = $this->applyOffers($bill->itemList);
        $bill = $this->addCustomerDetails($bill);


        $bill->billdate =  Carbon::createFromFormat('Y-m-d H:i:s', $bill->created_at)->format('d M Y');
        $bill->billdatetime =  Carbon::createFromFormat('Y-m-d H:i:s', $bill->created_at)->format($this->rDateFormat.' H:i');


        foreach ($bill->itemList as $bilItems) {
            $arrItem= array();
            $arrItem[0] = $bilItems->item;
            $arrItem = $this->applyOffers($arrItem);

            $bilItems =$this->makeBillingItemObject($bilItems, $arrItem[0]);
        }

    }

    return Response(array(
        'getBill' => $bill,
        'arrVatSummary' => $arrBillVatSummary,        
    ),200);

}catch(Exception $e){
    Log::error("BillingsController@activateBilling ".$e);
    return View("errors.exception");
} 

}


public function saveLoyaltyPoints($customer_id,$billing_id){

    try{
        $mallDetails = $this->mallDetails();
        if($mallDetails['loyalty'] == 1){
            if($customer_id != 0 && $customer_id != null && $billing_id != null && $billing_id != 0){            
                $customerData = Customer::find($customer_id);
                $loyalty = Loyalty::all();
                $points = 0;    
                $eligibility =0;  
                $earnedPoints = 0;  

                if(!empty($customerData) && !empty($loyalty)){
                    $loyalty_activation = $loyalty[0]->value;
                    $loyalty_points_on_sale = $loyalty[1]->value;
                    $loyalty_points = $loyalty[2]->value;

                    $bill = Billing::where('customer_id','=',$customer_id)
                    ->where('id','=',$billing_id)->first();
                    if(!empty($bill)){
                        $billAmt_ = Billing::where('customer_id','=',$customer_id)
                        ->where('id','=',$billing_id)
                        ->sum('bil_amt');
                        $billReturnAmt_ = BillReturns::join('billings','billings.id','bill_returns.billing_id')
                        ->where('billings.customer_id','=',$customer_id)

                        ->where('billing_id','=',$billing_id)
                        ->sum('bill_returns.amount');

                        $billAmt_ = $billAmt_ - $billReturnAmt_;
                        if($loyalty_points_on_sale != 0){
                            $earnedPoints = ($billAmt_/$loyalty_points_on_sale);
                        }else{
                            $earnedPoints = 0;
                        }


                        \DB::table('billings')
                        ->where('id' , '=', $billing_id)
                        ->update(array(
                            "earned_points"=>$earnedPoints,
                        ));

                        $totalBillAmt = Billing::where('customer_id','=',$customer_id)
                        ->sum('bil_amt');

                        $totalBillReturnAmt = BillReturns::join('billings','billings.id','bill_returns.billing_id')
                        ->where('billings.customer_id','=',$customer_id)

                        ->sum('bill_returns.amount');

                        $totalBillAmt = $totalBillAmt - $totalBillReturnAmt;
                        if($loyalty_activation > $totalBillAmt ){
                           $eligibility = 0;
                       }else{
                           $eligibility = 1; 
                       }

                       $custPoints = $customerData->points + $earnedPoints - $bill->loyalty_points;                  



                       \DB::table('customers')
                       ->where('id' , '=', $customer_id)
                       ->update(array(
                        "eligibility"=>$eligibility,
                        "points"=>$custPoints,
                    )); 
                   }

               }
           }
       }

   }catch(Exception $e){
    Log::error("BillingsController@saveLoyaltyPoints ".$e);
    return View("errors.exception");
} 
}

public function updateLoyaltyPoints($customer_id,$billing_id,$objBilling){

    try{
        $mallDetails = $this->mallDetails();
        if($mallDetails['loyalty'] == 1){
            if($customer_id != 0 && $customer_id != null && $billing_id != null && $billing_id != 0){            
                $customerData = Customer::find($customer_id);
                $loyalty = Loyalty::all();
                $points = 0;    
                $eligibility =0;  
                $earnedPoints = 0;  

                if(!empty($customerData) && !empty($loyalty)){
                    $loyalty_activation = $loyalty[0]->value;
                    $loyalty_points_on_sale = $loyalty[1]->value;
                    $loyalty_points = $loyalty[2]->value;

                    $bill = Billing::where('customer_id','=',$customer_id)
                    ->where('id','=',$billing_id)->first();
                    if(!empty($bill)){
                        $billAmt_ = Billing::where('customer_id','=',$customer_id)
                        ->where('id','=',$billing_id)
                        ->sum('bil_amt');
                        $billReturnAmt_ = BillReturns::join('billings','billings.id','bill_returns.billing_id')
                        ->where('billings.customer_id','=',$customer_id)
                        ->where('billing_id','=',$billing_id)
                        ->sum('bill_returns.amount');

                        $billAmt_ = $billAmt_ - $billReturnAmt_;
                        if($loyalty_points_on_sale != 0){
                            $earnedPoints = ($billAmt_/$loyalty_points_on_sale);
                        }else{
                            $earnedPoints = 0;
                        }


                        \DB::table('billings')
                        ->where('id' , '=', $billing_id)
                        ->update(array(
                            "earned_points"=>$earnedPoints,
                        ));

                        $totalBillAmt = Billing::where('customer_id','=',$customer_id)
                        ->sum('bil_amt');

                        $totalBillReturnAmt = BillReturns::join('billings','billings.id','bill_returns.billing_id')
                        ->where('billings.customer_id','=',$customer_id)
                        ->sum('bill_returns.amount');

                        $totalBillAmt = $totalBillAmt - $totalBillReturnAmt;
                        if($loyalty_activation > $totalBillAmt ){
                           $eligibility = 0;
                       }else{
                           $eligibility = 1; 
                       }

                       $custPoints = $customerData->points + ($earnedPoints - $bill->earned_points) + ($bill->loyalty_points - $objBilling->loyalty_points);                  

                       \DB::table('customers')
                       ->where('id' , '=', $customer_id)
                       ->update(array(
                        "eligibility"=>$eligibility,
                        "points"=>$custPoints,
                    )); 
                   }

               }
           }
       }

   }catch(Exception $e){
    Log::error("BillingsController@updateLoyaltyPoints ".$e);
    return View("errors.exception");
}

}

public function saveReturnLoyaltyPoints($customer_id,$billing_id){

    try{
        $mallDetails = $this->mallDetails();
        if($mallDetails['loyalty'] == 1){
            if($customer_id != 0 && $customer_id != null && $billing_id != null && $billing_id != 0){            
                $customerData = Customer::find($customer_id);
                $loyalty = Loyalty::all();
                $points = 0;    
                $eligibility =0;  
                $earnedPoints = 0;  

                if(!empty($customerData) && !empty($loyalty)){
                    $loyalty_activation = $loyalty[0]->value;
                    $loyalty_points_on_sale = $loyalty[1]->value;
                    $loyalty_points = $loyalty[2]->value;


                    $bill = Billing::where('customer_id','=',$customer_id)
                    ->where('id','=',$billing_id)->first();
                    if(!empty($bill)){
                       $billReturnAmt_ = BillReturns::join('billings','billings.id','bill_returns.billing_id')
                       ->where('billings.customer_id','=',$customer_id)
                       ->where('billing_id','=',$billing_id)
                       ->sum('bill_returns.amount');
                       if($bill->bil_amt != 0 && $bill->earned_points != 0){
                           $loyaltyearnedPointsAmt = $bill->bil_amt / $bill->earned_points;
                       }else{
                        $loyaltyearnedPointsAmt = 0;
                    }
                    $billAmt_ = $bill->bil_amt - $billReturnAmt_;

                    if($bill->bil_amt != 0 && $loyaltyearnedPointsAmt != 0){
                        $earnedPoints = $billAmt_ / $loyaltyearnedPointsAmt;
                    }else{
                        $earnedPoints = 0;
                    }
                    $totalBillAmt = Billing::where('customer_id','=',$customer_id)
                    ->sum('bil_amt');

                    $totalBillReturnAmt = BillReturns::join('billings','billings.id','bill_returns.billing_id')
                    ->where('billings.customer_id','=',$customer_id)
                    ->sum('bill_returns.amount');

                    $totalBillAmt = $totalBillAmt - $totalBillReturnAmt;
                    if($loyalty_activation > $totalBillAmt ){
                       $eligibility = 0;
                   }else{
                       $eligibility = 1; 
                   }

                   $custPoints = $customerData->points - ($bill->earned_points - $earnedPoints);
               }



               \DB::table('billings')
               ->where('id' , '=', $billing_id)
               ->update(array(
                "earned_points"=> $earnedPoints,
            ));

               \DB::table('customers')
               ->where('id' , '=', $customer_id)
               ->update(array(
                "eligibility"=>$eligibility,
                "points"=>$custPoints,
            )); 
           }
       }
   }

}catch(Exception $e){
    Log::error("BillingsController@saveReturnLoyaltyPoints ".$e);
    return View("errors.exception");
}
}

public function savePrint(Request $Request,$billId){
    try{

        if (Auth::check())
            {
                $userId = \Auth::id();
            }

            if($this->getAutAccess($Request) == 2){
                $itemReturn = BillReturns::with(['rItem','bill'])->where('bill_returns.billing_id','=', $billId)->get();
                $bill = Billing::with(['itemList','homeDeliveryCustomer','_customer', "itemList.item.units",'itemList.item.categories',"itemList.item.offer.item.units","itemList.item.packageList.items.units","taxRefund"])
                ->where("billing_status", "not like", "hold")
                ->where('id','=', $billId)->first();

        /*$arrBillVatSummary = BillingItems::join('billings','billings.id','billing_items.billing_id')->where('billing_items.billing_id','=', $billId)
        ->groupBy('vat')
        ->selectRaw('sum( ((billing_items.rate*100)/(100+billing_items.vat)) * billing_items.qty) as sale_amt , sum(billing_items.rate * billing_items.qty) as net_amt , billing_items.vat as vat_percent , sum( (billing_items.rate - ((billing_items.rate*100)/(100+billing_items.vat))) * billing_items.qty ) as tax_amt' )
        ->get();*/


        if($this->intVatType != 2){
            $arrBillVatSummary = BillingItems::join('billings','billings.id', '=','billing_items.billing_id' )
            ->where('billing_items.billing_id','=', $billId)
            ->groupBy('billing_items.vat')
            ->selectRaw('sum( (billing_items.rate - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) - (((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt))) - ((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(billing_items.vat)))) ) * billing_items.qty) as sale_amt , sum((billing_items.rate - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))* billing_items.qty) as net_amt , billing_items.vat as vat_percent , sum( (((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt))) - ((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(billing_items.vat)))) * (billing_items.qty) ) as tax_amt' )
            ->get();
        }else{
            $arrBillVatSummary = BillingItems::join('billings','billings.id', '=','billing_items.billing_id' )
            ->where('billing_items.billing_id','=', $billId)
            ->groupBy('billing_items.vat')
            ->selectRaw('sum( (billing_items.rate - 
                ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) as sale_amt , 
                sum(((billing_items.rate - 
                    ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) + ( (((billing_items.rate - 
                        ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) * billing_items.vat) /100 )) as net_amt ,
                        billing_items.vat as vat_percent , 
                        sum(( (((billing_items.rate - 
                            ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) * billing_items.vat) /100 )) as tax_amt' )
            ->get();
        }
        


    }elseif($this->getAutAccess($Request) == 1){
        $itemReturn = BillReturns::with(['rItem','bill', "rItem.items.units",'rItem.items.categories',"rItem.items.offer.item.units","rItem.items.packageList.items.units",'bill._customer'])
        ->join('billings','billings.id', '=','bill_returns.billing_id' )
        ->where( 'billings.user_id', '=', $userId )
        ->where('bill_returns.billing_id','=', $billId)
        ->select(['bill_returns.*', 'bill_returns.id as retid'])
        ->get();

        $bill = Billing::with(['itemList','homeDeliveryCustomer','_customer', "itemList.item.units",'itemList.item.categories',"itemList.item.offer.item.units","itemList.item.packageList.items.units","taxRefund"])
        ->where("billing_status", "not like", "hold")
        ->where('id','=', $billId)
        ->where('user_id', '=', $userId )->first();

        /*$arrBillVatSummary = BillingItems::join('billings','billings.id', '=','billing_items.billing_id' )
        ->where('billing_items.billing_id','=', $billId)
        ->where('billings.user_id', '=', $userId )
        ->groupBy('billing_items.vat')
        ->selectRaw('sum(((billing_items.rate*100)/(100+billing_items.vat)) * billing_items.qty) as sale_amt , sum(billing_items.rate * billing_items.qty) as net_amt , billing_items.vat as vat_percent , sum( ( billing_items.rate - ((billing_items.rate*100)/(100+billing_items.vat)) ) * billing_items.qty) as tax_amt' )
        ->get();*/

        if($this->intVatType !=2 ){
            $arrBillVatSummary = BillingItems::join('billings','billings.id', '=','billing_items.billing_id' )
            ->where('billing_items.billing_id','=', $billId)
            ->where('billings.user_id', '=', $userId )
            ->groupBy('billing_items.vat')
            ->selectRaw('sum( (billing_items.rate - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) - (((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt))) - ((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(billing_items.vat)))) ) * billing_items.qty) as sale_amt , sum((billing_items.rate - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))* billing_items.qty) as net_amt , billing_items.vat as vat_percent , sum( (((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt))) - ((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(billing_items.vat)))) * (billing_items.qty) ) as tax_amt' )
            ->get();
        }else{
            $arrBillVatSummary = BillingItems::join('billings','billings.id', '=','billing_items.billing_id' )
            ->where('billing_items.billing_id','=', $billId)
            ->where('billings.user_id', '=', $userId )
            ->groupBy('billing_items.vat')
            ->selectRaw('sum( (billing_items.rate - 
                ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) as sale_amt , 
                sum(((billing_items.rate - 
                    ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) + ( (((billing_items.rate - 
                        ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) * billing_items.vat) /100 )) as net_amt ,
                        billing_items.vat as vat_percent , 
                        sum(( (((billing_items.rate - 
                            ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) * billing_items.vat) /100 )) as tax_amt' )
            ->get();
        }
        

    }else{
        $itemReturn =[];
        $bill =[]; 
        $arrBillVatSummary = [];
    }


    if(count($itemReturn)){




        /** create item object as per requirment in return bill object  */
        foreach ($itemReturn as $returnI) {
            /** Added dist_amt for calculation of totalAmount in vuejs */
            $returnI = $this->addCustomerDetails($returnI);
            $returnI['dis_amt']=0;
            foreach ($returnI->rItem as $rItem) {
                $rItem =$this->makeBillingItemObject($rItem, $rItem->items);
            }
        }
    }

    if(!empty($bill)){

        $bill->itemList = $this->applyOffers($bill->itemList);


        $bill = $this->addCustomerDetails($bill);
        $bill = $this->getQtyBillToReturn($bill,$itemReturn);

        $bill->billdate =  Carbon::createFromFormat('Y-m-d H:i:s', $bill->created_at)->format('d M Y');
        $bill->billdatetime =  Carbon::createFromFormat('Y-m-d H:i:s', $bill->created_at)->format($this->rDateFormat.' H:i');


        foreach ($bill->itemList as $bilItems) {
            $arrItem= array();
            $arrItem[0] = $bilItems->item;
            $arrItem = $this->applyOffers($arrItem);

            $bilItems =$this->makeBillingItemObject($bilItems, $arrItem[0]);
        }

    }

    return array(
        'getBill' => $bill,
        'getreturnBill' => $itemReturn,
        'arrVatSummary' => $arrBillVatSummary,
    );

}catch(Exception $e){
    Log::error("BillingsController@savePrint ".$e);
    return View("errors.exception");
}
}

public function getCustomerByPassport(Request $request){
    try{
      $inputData = $request->all()["data"];
      //$str=preg_replace('/\s+/', '', $inputData['mrz']);
       $data= array('mrz'=>$inputData['mrz']);
      //$authorization=$request->data['bearerToken'];
    
      $url="https://frontoffice.tax.planetpayment.ae/services/transactions/api/v2/mrz-decoded";
      $authorization = "Authorization:".$inputData['bearerToken'];
     $send=json_encode($data);
       //$data1= json_decode(($send),true);
     //
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
      curl_setopt($ch, CURLOPT_TIMEOUT, 10);
      curl_setopt($ch, CURLOPT_POST,1 );
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json',$authorization ));
    //   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_POSTFIELDS,$send);
        
      $httpCode = curl_getinfo($ch , CURLINFO_HTTP_CODE); // this results 0 every time
      $response = curl_exec($ch);
      
      if ($response === false) 
          $response = curl_error($ch);
      
      return stripslashes($response);
    }
    catch(Exception $e){
        Log::error("BillingsController@getCustomerByPassport ".$e);
        return View("errors.exception");
    }
}

public function getexistingtaxRefund(Request $Request){
    try{
        if (Auth::check())
        {
            $userId = \Auth::id();
        }
        $tag_number =  $Request->all()['data'];
        $billId1=BillingTaxRefund::where('taxRefundTagNumber','=',$tag_number)->first();
        if($billId1!=null){
         $billId=$billId1->billId;
         if($this->getAutAccess($Request) == 2){
            $itemReturn = BillReturns::with(['rItem','bill'])->where('bill_returns.billing_id','=', $billId)->get();
            $bill = Billing::with(['itemList','homeDeliveryCustomer','_customer', "itemList.item.units",'itemList.item.categories',"itemList.item.offer.item.units","itemList.item.packageList.items.units","taxRefund"])
            ->where("billing_status", "not like", "hold")
            ->where('id','=', $billId)->first();

    /*$arrBillVatSummary = BillingItems::where('billing_id','=', $billId)
    ->groupBy('vat')
    ->selectRaw('sum( ((rate*100)/(100+vat)) * qty) as sale_amt , sum(rate * qty) as net_amt , vat as vat_percent , sum((rate - ((rate * 100)/(100 + vat))) * qty) as tax_amt' )
    ->get();*/


    if($this->intVatType !=2){
        $arrBillVatSummary = BillingItems::join('billings','billings.id', '=','billing_items.billing_id' )
        ->where('billing_items.billing_id','=', $billId)
        ->groupBy('billing_items.vat')
        ->selectRaw('sum( (billing_items.rate - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) - (((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt))) - ((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(billing_items.vat)))) ) * billing_items.qty) as sale_amt , sum((billing_items.rate - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))* billing_items.qty) as net_amt , billing_items.vat as vat_percent , sum( (((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt))) - ((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(billing_items.vat)))) * (billing_items.qty) ) as tax_amt' )
        ->get();

    }else{
        $arrBillVatSummary = BillingItems::join('billings','billings.id', '=','billing_items.billing_id' )
        ->where('billing_items.billing_id','=', $billId)
        ->groupBy('billing_items.vat')
        ->selectRaw('sum( (billing_items.rate - 
            ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) as sale_amt , 
            sum(((billing_items.rate - 
                ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) + ( (((billing_items.rate - 
                    ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) * billing_items.vat) /100 )) as net_amt ,
                    billing_items.vat as vat_percent , 
                    sum(( (((billing_items.rate - 
                        ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) * billing_items.vat) /100 )) as tax_amt' )
        ->get();
    }
    


}elseif($this->getAutAccess($Request) == 1){
    $itemReturn = BillReturns::with(['rItem','bill', "rItem.items.units",'rItem.items.categories',"rItem.items.offer.item.units","rItem.items.packageList.items.units",'bill._customer'])
    ->join('billings','billings.id', '=','bill_returns.billing_id' )
    ->where( 'billings.user_id', '=', $userId )
    ->where('bill_returns.billing_id','=', $billId)
    ->select(['bill_returns.*', 'bill_returns.id as retid'])
    ->get();

    $bill = Billing::with(['itemList','homeDeliveryCustomer','_customer', "itemList.item.units",'itemList.item.categories',"itemList.item.offer.item.units","itemList.item.packageList.items.units","taxRefund"])
    ->where("billing_status", "not like", "hold")
    ->where('id','=', $billId)
    ->where('user_id', '=', $userId )->first();


    if($this->intVatType !=2){
        $arrBillVatSummary = BillingItems::join('billings','billings.id', '=','billing_items.billing_id' )
        ->where('billing_items.billing_id','=', $billId)
        ->where('billings.user_id', '=', $userId )
        ->groupBy('billing_items.vat')
        ->selectRaw('sum( (billing_items.rate - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) - (((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt))) - ((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(billing_items.vat)))) ) * billing_items.qty) as sale_amt , 
            sum((billing_items.rate - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))* billing_items.qty) as net_amt ,
            billing_items.vat as vat_percent , 
            sum( (((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt))) - ((((billing_items.rate) - ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)))*100)/(100+(billing_items.vat)))) * (billing_items.qty) ) as tax_amt' )
        ->get();
    }else{
        $arrBillVatSummary = BillingItems::join('billings','billings.id', '=','billing_items.billing_id' )
        ->where('billing_items.billing_id','=', $billId)
        ->where('billings.user_id', '=', $userId )
        ->groupBy('billing_items.vat')
        ->selectRaw('sum( (billing_items.rate - 
            ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) as sale_amt , 
            sum(((billing_items.rate - 
                ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) + ( (((billing_items.rate - 
                    ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) * billing_items.vat) /100 )) as net_amt ,
                    billing_items.vat as vat_percent , 
                    sum(( (((billing_items.rate - 
                        ((billings.dis_amt * billing_items.rate)/(billings.subtotal + billings.v_amt)) ) * billing_items.qty) * billing_items.vat) /100 )) as tax_amt' )
        ->get();

    }


}else{
    $itemReturn =[];
    $bill =[]; 
    $arrBillVatSummary = [];
}


if(count($itemReturn)){


    /** create item object as per requirment in return bill object  */
    foreach ($itemReturn as $returnI) {
        /** Added dist_amt for calculation of totalAmount in vuejs */
        $returnI = $this->addCustomerDetails($returnI);
        $returnI['dis_amt']=0;
        foreach ($returnI->rItem as $rItem) {
            $rItem =$this->makeBillingItemObject($rItem, $rItem->items);
        }
    }
}

if(!empty($bill)){


    $bill->itemList = $this->applyOffers($bill->itemList);


    $bill = $this->addCustomerDetails($bill);
    $bill = $this->getQtyBillToReturn($bill,$itemReturn);

    $bill->billdate =  Carbon::createFromFormat('Y-m-d H:i:s', $bill->created_at)->format('d M Y');
    $bill->billdatetime =  Carbon::createFromFormat('Y-m-d H:i:s', $bill->created_at)->format($this->rDateFormat.' H:i');


    foreach ($bill->itemList as $bilItems) {
        $arrItem= array();
        $arrItem[0] = $bilItems->item;
        $arrItem = $this->applyOffers($arrItem);

        $bilItems =$this->makeBillingItemObject($bilItems, $arrItem[0]);
    }

}

        }
        else{
          $bill=null;
          $itemReturn=[]; 
          $arrBillVatSummary=[];
        }
     

       
return Response(array(
    'getBill' => $bill,
    'getreturnBill' => $itemReturn,
    'arrVatSummary' => $arrBillVatSummary,
),200);
    }
    catch(Exception $e){
        Log::error("BillingsController@getexistingtaxRefund".$e);
        return View("errors.exception");
    }
}
}

