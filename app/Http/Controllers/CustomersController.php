<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request; 
use App\Customer;
use Carbon\Carbon;
use Toastr;
use Excel;
use PDF;
use Mail;
use Illuminate\Support\Facades\Input;
use App\Billing;
use App\BillReturns;
use App\CustomerReceipt;
use App\Loyalty;
use App\CountryCode;
use Log;
use Common;
use Exception;

class CustomersController extends Controller
{
  private $rDateFormat;

  public function __construct(){
    $this->rDateFormat = Common::rDateFormat();
  }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) 
    {
      try
      {
       
        $arrReq = $request->all();
       // $intNewId = DB::select("SELECT auto_increment FROM INFORMATION_SCHEMA.TABLES WHERE table_name = 'customers' ");

        $intNewId = \Common::get_autoIncremenetVal("customers");

        $strFilter = (isset($arrReq['filter'])) ? $arrReq['filter'] : null ;
        $intPoints = (isset($arrReq['points'])) ? $arrReq['points'] : null ;
        $bolnEligibility = (isset($arrReq['eligibility'])) ? $arrReq['eligibility'] : null ;
        $search = $request->input('searchkey','');

        /*get customer details as per filter send 5th variable as false if this is action of master customer */
        $arrCustomers = $this->getCustomerMasterDetailsAsPerFilter($strFilter,$intPoints,$bolnEligibility,$search,'false');
        
       $nationality=CountryCode::all();
       $countryOfResidence=CountryCode::all();
        if(!$request->ajax()){
          return View('pages.masters.customers.customer',[
            'customers'=> $arrCustomers,
            'intNewId'    => $intNewId,
            'filter'   =>$strFilter,
            'points'=> $intPoints,
            'eligibility'   =>$bolnEligibility,
            'searchkey' => $request->input('searchkey'),
            'nationality'=>$nationality,
            'countryOfResidence'=>$countryOfResidence
          ]);    
        }else{
          return Response([
            'customers'=> $arrCustomers,
            'intNewId'    => $intNewId,
            'filter'   =>$strFilter,
            'points'=> $intPoints,
            'eligibility'   =>$bolnEligibility,
            'searchkey' => $request->input('searchkey'),
            'nationality'=>$nationality,
            'countryOfResidence'=>$countryOfResidence
            
          ], 200 );    
        }
      }catch(Exception $e){
        Log::error("CustomersController@index ".$e);
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
  
      $this->validate($request, [
        'phone' => 'nullable | unique:customers|max:255',
        'mobile' => 'required|unique:customers|max:255',
        'email' => 'nullable|unique:customers|max:255',
        'name' => 'required',
       
      ],[
        'email.unique' => "The email has already been taken.",
      ]);

      try{
         $task = Customer::create($request->all());

        toastr::success('Success', trans("validation.success_store",[ "attribute" => trans('glob.customer') ]));
        return redirect()->action('CustomersController@index');

      }catch(Exception $e){
        Log::error("CustomersController@store ".$e);
        return View("errors.exception");
      }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response 
     */
    public function edit($id)
    {
      try{
        $filter = '';
        $points = '';
        $eligibility = '';
        $customers = Customer::paginate(25);
        $editCustomer = Customer::where('id', $id)->first(); 
        $nationality=CountryCode::all();
       $countryOfResidence=CountryCode::all();
        return View('pages.masters.customers.customer',[
          'customers'=> $customers,
          'editCustomer'=> $editCustomer,
          'filter'   =>$filter,
          'points'=> $points,
          'eligibility'   =>$eligibility,
          'searchkey' => '',
          'nationality'=>$nationality,
          'countryOfResidence'=>$countryOfResidence
        ]);

      }catch(Exception $e){
        Log::error("CustomersController@edit ".$e);
        return View("errors.exception");
      }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    { 

      $this->validate($request, [
        'phone' => 'nullable |unique:customers,phone,'.$id.'|max:255',
        'mobile' => 'required|unique:customers,mobile,'.$id.'|max:255',
        'email' => 'nullable|unique:customers,email,'.$id.'|max:255',
        'name' => 'required',
      ],[
        'email.unique' => "The email has already been taken.",
      ]);


      try{
        $customerData = Customer::find($id);
        if(!is_null($customerData)){
          $input = $request->all();
          $customerData->fill($input)->save();
          toastr::success('Success', trans("validation.success_update",[ "attribute" => trans('glob.customer') ]));
          return redirect()->action('CustomersController@index');
        }else{
          toastr::warning('Warning', trans("validation.warning_update_deleted_item",["attribute" => trans('glob.customer') ]));
          return redirect()->action('CustomersController@index');
        }
      }catch(Exception $e){
        Log::error("CustomersController@update ".$e);
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
        $isBill = Billing::where("customer_id","=",$id)->first();
        $isReceipt = CustomerReceipt::where("customer_id","=",$id)->first();
        if(sizeof($isBill) == 0 && sizeof($isReceipt) == 0){
          $customer  = Customer::find($id);
          (!is_null($customer)) ?  ($r = $customer->delete()) : ($r = null);
          if($r)
            $res = array('status' =>  "success", "data" =>array(), "message" => trans("validation.success_delete",["attribute" => trans('glob.customer') ]) );
          else
            $res = array('status' =>  "error", "data" =>array(), "message" => trans("validation.error_delete",["attribute" => trans('glob.customer') ]));
        }else{
          if(sizeof($isBill) > 0)
            $res = array('status' =>  "warning", "data" =>array(),  "message" =>' Customer have Bill, Cant delete Customer!' );
          else if(sizeof($isReceipt) > 0)
            $res = array('status' =>  "warning", "data" =>array(),  "message" =>'Customer have Credit Receipt, Cant delete Customer!' );
        }
        return response($res, 200);
      }catch(Exception $e){
        Log::error("CustomersController@destroy ".$e);
        return View("errors.exception");
      }
    }

    /**
     * birthdays list for reports
     * @return  \report\birthdays
     */
    public function getBirthdays(Request $request){
      try{
        $searchkey = $request->input('searchkey', '');
        // $filter = $request->input('filter', 1);
       // return $now = Carbon::now()->format('M');
        $date1 = $request->input('date1', Carbon::now()->day);
        $month1 =$request->input('month1',Carbon::now()->month);
        $date2 = $request->input('date2', Carbon::now()->day);
        $month2 = $request->input('month2',Carbon::now()->month);
        $day =[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31];
        $month=array(1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec');
        $carbonTime = Carbon::now();
        $todayDate = $carbonTime->toDateString();
        /* get all details of birthdays*/
       $birthdays = $this->getBirthdayData($searchkey,$todayDate,$date1,$month1,$date2,$month2,'birthday');
        return View('pages.reports.birthday_list.birthday',[
          'birthdays'      => $birthdays,
          'date_format' => $this->rDateFormat,
          'searchkey'   => $searchkey,
          'date1'=>$date1,
          'month1'=>$month1,
          'date2'=>$date2,
          'month2'=>$month2,
          'day'=>$day,
          'month'=>$month
         
        ]); 
      }catch(Exception $e){
        Log::error("CustomersController@getBirthdays ".$e);
        return View("errors.exception");
      }
    }

    /**
     * anniversary list for reports
     * @return  \report\birthdays
     */
    public function getAnniversary(Request $request){
      try{
        $searchkey = $request->input('searchkey', '');
        $filter = $request->input('filter', 1);
        $carbonTime = Carbon::now();
        $todayDate = $carbonTime->toDateString();
        /* get all details of anivarsary*/
        $anniversary = $this->getBirthdayAnivarsaryData($searchkey,$todayDate,$filter,'anniversary');
        return View('pages.reports.anniversary_list.anniversary',[
          'anniversary'      => $anniversary,
          'date_format' => $this->rDateFormat,
          'searchkey'   => $searchkey,
          'filter'  => $filter,
        ]); 
      }catch(Exception $e){
        Log::error("CustomersController@getAnniversary ".$e);
        return View("errors.exception");
      }
    }   



    public function getCustomerList(Request $request){
      try
      {
        $arrReq = $request->all();
        $strFilter = (isset($arrReq['filter'])) ? $arrReq['filter'] : null ;
        $intPoints = (isset($arrReq['points'])) ? $arrReq['points'] : null ;
        $bolnEligibility = (isset($arrReq['eligibility'])) ? $arrReq['eligibility'] : null ;
        $search = $request->input('searchkey','');
        /*get customer list as per filter/ send 5th variable as true if this is action of repots*/
        $arrCustomers = $this->getCustomerMasterDetailsAsPerFilter($strFilter,$intPoints,$bolnEligibility,$search,'true');
        return View('pages.reports.customer_list.customer_list',[
          'customers'      => $arrCustomers,
          'filter'   =>$strFilter,
          'points'=> $intPoints,
          'eligibility'   =>$bolnEligibility,
          'searchkey' => $request->input('searchkey'),
        ]); 
      }
      catch(Exception $e){
        Log::error("CustomersController@getCustomerList ".$e);
        return View("errors.exception");
      }

    }

    /** export to xls Customer list */
    public function exportToXls(Request $request){
      try
      {
       $arrReq = $request->all();
       $strFilter = (isset($arrReq['filter'])) ? $arrReq['filter'] : null ;
       $intPoints = (isset($arrReq['points'])) ? $arrReq['points'] : null ;
       $bolnEligibility = (isset($arrReq['eligibility'])) ? $arrReq['eligibility'] : null ;
       $search = (isset($arrReq['search'])) ? $arrReq['search'] : null ;
       $type = (isset($arrReq['type'])) ? $arrReq['type'] : 'false' ;

       /*get customer details as per filter/ send 5th variable as false if this is action of master customer*/
       $arrCustomers = $this->getExportCustomerMasterDetailsAsPerFilter($strFilter,$intPoints,$bolnEligibility,$search,''.$type);


       if(sizeof($arrCustomers)){
        $carbon = Carbon::today();
        $timestamp = $carbon->timestamp;
        $format = $carbon->format('Y-m-d');
        $exportName = 'Customer'.$format;

        \App\Legacy\LegacyExcel::create($exportName, function ($excel) use($arrCustomers){

          $excel->sheet('Customer', function ($sheet) use($arrCustomers){

            $itemList = array();
            foreach ($arrCustomers as $o) {              
             $o->eligibility = ($o->eligibility == 0 ) ?  'Not Eligible': 'Eligible' ;
             if($o->gender ==  1){
              $o->gender = 'Male';
            }elseif ($o->gender ==  2) {
              $o->gender = 'Female'; 
            }else{
              $o->gender = 'N/A';
            }

            if($o->age_group ==  1){
              $o->age_group = '18 to 29';
            }elseif ($o->age_group ==  2) {
              $o->age_group = '30 to 36'; 
            }elseif ($o->age_group ==  3) {
              $o->age_group = '37 to 45'; 
            }elseif ($o->age_group ==  4) {
              $o->age_group = '46 to 55'; 
            }elseif ($o->age_group ==  5) {
              $o->age_group = '56+'; 
            }else{
              $o->age_group = 'N/A';
            }

            if($o->prefered_mode ==  1){
              $o->prefered_mode = trans('glob.email');
            }elseif ($o->prefered_mode ==  2) {
              $o->prefered_mode = trans('glob.phone'); 
            }elseif ($o->prefered_mode ==  3) {
              $o->prefered_mode = trans('glob.sms'); 
            }elseif ($o->prefered_mode ==  4) {
              $o->prefered_mode = trans('glob.whatsapp'); 
            }else{
              $o->prefered_mode = 'N/A';
            }
           if($o->address==null){
            $o->address='N/A';
           }
           if($o->prefered_language == 1){
            $o->prefered_language =  trans('glob.english');
           }elseif ($o->prefered_language == 2) {
            $o->prefered_language =  trans('glob.arabic');
           }else{
            $o->prefered_language = 'N/A';
           }
           array_push($itemList, array(
             trans('glob.id')  => $o->id,
             trans('glob.name')  => $o->name, 
           
             trans('glob.gender')  => $o->gender,
             trans('glob.age_group')  => $o->age_group,
             trans('glob.prefered_mode')  => $o->prefered_mode,
             trans('glob.prefered_language')  => $o->prefered_language,
             trans('customer.spouse')  => $o->spouse, 
             trans('glob.comment')  => $o->address, 
             trans('glob.mobile')  => $o->mobile, 
             trans('glob.email')  => $o->email, 
             trans('glob.city')  => $o->city,
             trans('glob.nationality')  => $o->countryCode['code2'],
             trans('glob.documentType')  => $o->type,
             trans('glob.passportnumber')  => $o->number,
             trans('glob.eligibility')  => $o->eligibility,
             trans('glob.points')  => \Common::fixIt($o->points),

           )
         );
         }              

         /* setting column names for data - you can of course set it manually */
         $sheet->appendRow(array_keys($itemList[0])); /* column names */

         /* getting last row number (the one we already filled and setting it to bold */
           $sheet->row($sheet->getHighestRow(), function ($row) {
            $row->setFontWeight('bold');
          });

           /* putting users data as next rows */
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
      Log::error("CustomersController@exportToXls ".$e);
      return View("errors.exception");
    }
  }
  /** export to PDF Customer list */
  public function exportToPdf(Request $request){

    try
    {
      $arrReq = $request->all();
      $strFilter = (isset($arrReq['filter'])) ? $arrReq['filter'] : null ;
      $intPoints = (isset($arrReq['points'])) ? $arrReq['points'] : null ;
      $bolnEligibility = (isset($arrReq['eligibility'])) ? $arrReq['eligibility'] : null ;
      $search = (isset($arrReq['search'])) ? $arrReq['search'] : null ;
      $type = (isset($arrReq['type'])) ? $arrReq['type'] : 'false' ;

      /*get customer details as per filter / send 5th variable as false if this is action of master customer*/
      $arrCustomers = $this->getExportCustomerMasterDetailsAsPerFilter($strFilter,$intPoints,$bolnEligibility,$search,''.$type);

      if(sizeof($arrCustomers)){
        $pdf = PDF::loadView('pages.reports.customer_list.customer_pdf', [
          'customer' => $arrCustomers
        ]); 

        $pdf->setPaper('a4');
        $carbon = Carbon::today();
        $timestamp = $carbon->timestamp;
        $format = $carbon->format('Y-m-d');
        $exportName = 'Customer'.$format.'.pdf';
        return $pdf->download($exportName);

      }else{
        toastr::warning('Warning', trans("validation.export_error"));
        return back();
      }
    }
    catch(Exception $e){
      Log::error("CustomersController@exportToPdf ".$e);
      return View("errors.exception");
    }

  }

  public function getCustomerByMobile($mobile, Request $request){

    try
    {   
      $arrReq = $request->all();
      $flag = (isset($arrReq['flag'])) ? $arrReq['flag'] : '';
      $strField = '';
      $strFilter='';
      if($flag == 'customerMobile'){
       $strField ="mobile";
       $strFilter = "like";
     }
     else{
      if($flag == 'customerId'){
        $strField = "id";
        $strFilter = "=";
      }
    }
    $arrCustomer = Customer::with(['homeDCustomer','countryCode'])->where($strField, $strFilter,$mobile)->get();
    $arrLoyaltyProgram = Loyalty::all();
    $arrLoyalty = DB::select("SELECT options.value as loyalty FROM options where options.type = 'billing' and options.key = 'loyalty'");
    $bolnLoyaltyFlag = (isset($arrLoyalty[0]->loyalty)) ? $arrLoyalty[0]->loyalty : 0;
    $strMessage ='';
    $strEligible = '';
    $strMessage_point = '';
    if(count($arrCustomer) && count($arrLoyaltyProgram) && $bolnLoyaltyFlag){
      $intPurchaseAmount = 0;
      $intCustomerId = (isset($arrCustomer[0]->id)) ? $arrCustomer[0]->id : 0 ;

      $fltBillAmt_ = Billing::where('customer_id','=',$intCustomerId)
      ->sum('bil_amt');
      $fltBillReturnAmt_ = BillReturns::join('billings','billings.id','bill_returns.billing_id')
      ->where('billings.customer_id','=',$intCustomerId)
      ->sum('bill_returns.amount');
      $intPurchaseAmount = ($fltBillAmt_ - $fltBillReturnAmt_);


      if($intPurchaseAmount >= $arrLoyaltyProgram[0]->value && $arrCustomer[0]->eligibility == 1){
        $strEligible = 'Loyalty Eligible';
        if($arrCustomer[0]->points > $arrLoyaltyProgram[3]->value){
          $intReddemablePoints = \Common::fixIt($arrCustomer[0]->points - $arrLoyaltyProgram[3]->value);
          $strMessage_point = "";
          $strMessage = "Points = ".\Common::fixIt($arrCustomer[0]->points);
        }
        else{
          $intPintsNeeded = \Common::fixIt($arrLoyaltyProgram[3]->value - $arrCustomer[0]->points);
          if($intPintsNeeded >= 1){
            $strMessage = "Points ="." ". $intPintsNeeded. " More to Redeem";
          }
          else{
           $strMessage = "";
         }
         $strMessage_point = "Total Points = ".\Common::fixIt($arrCustomer[0]->points); 
       }
     }
     else{
      $strEligible = 'Loyalty Not Eligible';
      $intPurchaseNeeded = \Common::fixIt($arrLoyaltyProgram[0]->value - $intPurchaseAmount);
      $strMessage_point =  "Total Purchase ="." AED  ".\Common::fixIt($intPurchaseAmount);
      $strMessage = "Purchase ="." AED  ".$intPurchaseNeeded ." More to Eligible";
    }
  }

  if(count($arrCustomer)){
    if(count($arrCustomer[0]->homeDCustomer)){
      $arrCustomer[0]->homeDCustomer = $arrCustomer[0]->homeDCustomer->last();
    }else{
      $arrCustomer[0]->homeDCustomer = null;
    }  
  }
  if($request->ajax()){
    return Response(array(
      "customer" => $arrCustomer->first(),
      "message"=> $strMessage,
      "message_point"=> $strMessage_point,
      "eligible" => $strEligible
    ));
  }
}
catch(Exception $e){
  Log::error("CustomersController@getCustomerByMobile ".$e);
  return View("errors.exception");
}
}

public function getCustomerMasterDetailsAsPerFilter($strFilter,$intPoints,$bolnEligibility,$search,$type){

  try{
    if($strFilter!= null && $intPoints!=null && $bolnEligibility!=null){
      if($search!=null){
        $arrCustomers = Customer::with(['countryCode'])->where('points',$strFilter,$intPoints)
        ->where('eligibility','=',$bolnEligibility)
        ->where(function($query) use ($search,$type) {
          $query->orWhere('name', 'LIKE','%'.$search.'%')
          ->orWhere('city', 'LIKE','%'.$search.'%')
          ->orWhereRaw("CASE WHEN ".$type." THEN address LIKE '%".$search."%'  END")
          ->orWhereRaw("CASE WHEN ".$type." THEN email LIKE '%".$search."%'    END")
          ->orWhere('mobile', 'LIKE','%'.$search.'%')
          ->orWhere('number', 'LIKE','%'.$search.'%');
        })
        ->paginate(25);
      }else{
        $arrCustomers = Customer::with(['countryCode'])->where('points',$strFilter,$intPoints)
        ->where('eligibility','=',$bolnEligibility)
        ->paginate(25);
      }

    }else if($strFilter!=null && $intPoints!=null){
      if($search!=null){
        $arrCustomers = Customer::with(['countryCode'])->where('points',$strFilter,$intPoints)
        ->where(function($query) use ($search,$type) {
          $query->orWhere('name', 'LIKE','%'.$search.'%')
          ->orWhere('city', 'LIKE','%'.$search.'%')
          ->orwhereraw("CASE WHEN ".$type." THEN address LIKE '%".$search."%' END")
          ->orwhereraw("CASE WHEN ".$type." THEN email LIKE '%".$search."%'  END")
          ->orWhere('mobile', 'LIKE','%'.$search.'%')
          ->orWhere('number', 'LIKE','%'.$search.'%');
          
          
        })
        ->paginate(25);
      }else{
        $arrCustomers = Customer::with(['countryCode'])->where('points',$strFilter,$intPoints)
        ->where(function($query) use ($search,$type) {
          $query->orWhere('name', 'LIKE','%'.$search.'%')
          ->orWhere('city', 'LIKE','%'.$search.'%')
          ->orwhereraw("CASE WHEN ".$type."  THEN address LIKE '%".$search."%'  END")
          ->orwhereraw("CASE WHEN ".$type." THEN email LIKE '%".$search."%'   END")
          ->orWhere('mobile', 'LIKE','%'.$search.'%')
          ->orWhere('number', 'LIKE','%'.$search.'%');
          
        })
        ->paginate(25);
      }

    }else if($bolnEligibility!= null){

      if($search!=null){
        $arrCustomers = Customer::with(['countryCode'])->where('eligibility','=',$bolnEligibility)
        ->where(function($query) use ($search,$type) {
          $query->orWhere('name', 'LIKE','%'.$search.'%')
          ->orWhere('city', 'LIKE','%'.$search.'%')
          ->orwhereraw("CASE WHEN ".$type."  THEN address LIKE '%".$search."%'   END")
          ->orwhereraw("CASE WHEN ".$type."  THEN email LIKE '%".$search."%'   END")
          ->orWhere('mobile', 'LIKE','%'.$search.'%')
          ->orWhere('number', 'LIKE','%'.$search.'%');
          
        })
        ->paginate(25);
      }else{
        $arrCustomers = Customer::with(['countryCode'])->where('eligibility','=',$bolnEligibility)
        ->paginate(25);
      }

    }else{
      if($search!=null){
        $arrCustomers = Customer::with(['countryCode'])->orWhere('name', 'LIKE','%'.$search.'%')
        ->orWhere('city', 'LIKE','%'.$search.'%')
        ->orWhere('mobile', 'LIKE','%'.$search.'%')
        ->orWhere('number', 'LIKE','%'.$search.'%')
        ->orwhereraw("CASE WHEN ".$type." THEN address LIKE '%".$search."%'    END")
        ->orwhereraw("CASE WHEN ".$type." THEN email LIKE '%".$search."%'  END")
        ->paginate(25);
      }else{
        $arrCustomers = Customer::with(['countryCode'])->paginate(25);
      }

    }

    return $arrCustomers;

  }catch(Exception $e){
    Log::error("CustomersController@getCustomerMasterDetailsAsPerFilter ".$e);
    return View("errors.exception");
  }
}

public function getExportCustomerMasterDetailsAsPerFilter($strFilter,$intPoints,$bolnEligibility,$search,$type){


  try{
    if($strFilter!= null && $intPoints!=null && $bolnEligibility!=null){
      if($search!=null){
        $arrCustomers = Customer::with(['countryCode'])->where('points',$strFilter,$intPoints)
        ->where('eligibility','=',$bolnEligibility)
        ->where(function($query) use ($search,$type) {
          $query->orWhere('name', 'LIKE','%'.$search.'%')
          ->orWhere('city', 'LIKE','%'.$search.'%')
          ->orWhereRaw("CASE WHEN ".$type." THEN address LIKE '%".$search."%'  END")
          ->orWhereRaw("CASE WHEN ".$type." THEN email LIKE '%".$search."%'    END")
          ->orWhere('mobile', 'LIKE','%'.$search.'%')
          ->orWhere('number', 'LIKE','%'.$search.'%');
          
        })
        ->get();
      }else{
        $arrCustomers = Customer::with(['countryCode'])->where('points',$strFilter,$intPoints)
        ->where('eligibility','=',$bolnEligibility)
        ->get();
      }

    }else if($strFilter!=null && $intPoints!=null){
      if($search!=null){
        $arrCustomers = Customer::with(['countryCode'])->where('points',$strFilter,$intPoints)
        ->where(function($query) use ($search,$type) {
          $query->orWhere('name', 'LIKE','%'.$search.'%')
          ->orWhere('city', 'LIKE','%'.$search.'%')
          ->orwhereraw("CASE WHEN ".$type." THEN address LIKE '%".$search."%' END")
          ->orwhereraw("CASE WHEN ".$type." THEN email LIKE '%".$search."%'  END")
          ->orWhere('mobile', 'LIKE','%'.$search.'%')
          ->orWhere('number', 'LIKE','%'.$search.'%');
        })
        ->get();
      }else{
        $arrCustomers = Customer::with(['countryCode'])->where('points',$strFilter,$intPoints)
        ->where(function($query) use ($search,$type) {
          $query->orWhere('name', 'LIKE','%'.$search.'%')
          ->orWhere('city', 'LIKE','%'.$search.'%')
          ->orwhereraw("CASE WHEN ".$type."  THEN address LIKE '%".$search."%'  END")
          ->orwhereraw("CASE WHEN ".$type." THEN email LIKE '%".$search."%'   END")
          ->orWhere('mobile', 'LIKE','%'.$search.'%')
          ->orWhere('number', 'LIKE','%'.$search.'%');
          
        })
        ->get();
      }

    }else if($bolnEligibility!= null){

      if($search!=null){
        $arrCustomers = Customer::with(['countryCode'])->where('eligibility','=',$bolnEligibility)
        ->where(function($query) use ($search,$type) {
          $query->orWhere('name', 'LIKE','%'.$search.'%')
          ->orWhere('city', 'LIKE','%'.$search.'%')
          ->orwhereraw("CASE WHEN ".$type."  THEN address LIKE '%".$search."%'   END")
          ->orwhereraw("CASE WHEN ".$type."  THEN email LIKE '%".$search."%'   END")
          ->orWhere('mobile', 'LIKE','%'.$search.'%')
          ->orWhere('number', 'LIKE','%'.$search.'%');
          
        })
        ->get();
      }else{
        $arrCustomers = Customer::with(['countryCode'])->where('eligibility','=',$bolnEligibility)
        ->get();
      }

    }else{
      if($search!=null){
        $arrCustomers = Customer::with(['countryCode'])->orWhere('name', 'LIKE','%'.$search.'%')
        ->orWhere('city', 'LIKE','%'.$search.'%')
        ->orWhere('mobile', 'LIKE','%'.$search.'%')
        ->orWhere('number', 'LIKE','%'.$search.'%')
        ->orwhereraw("CASE WHEN ".$type." THEN address LIKE '%".$search."%'    END")
        ->orwhereraw("CASE WHEN ".$type." THEN email LIKE '%".$search."%'  END")
        ->get();
      }else{
         $arrCustomers = Customer::with(['countryCode'])->get();
      }

    }

    return $arrCustomers;

  }catch(Exception $e){
    Log::error("CustomersController@getExportCustomerMasterDetailsAsPerFilter ".$e);
    return View("errors.exception");
  }
}


public function getBirthdayAnivarsaryData($searchkey,$todayDate,$filter,$type ){

  try{
    if(trim($searchkey)==''){
      return Customer::where("$type","<>", null)
      ->whereRaw("CASE WHEN $filter = 1 THEN MONTH($type) = MONTH('$todayDate') AND  DAY($type) = DAY('$todayDate') AND YEAR($type) < YEAR('$todayDate') ELSE true END")
      ->paginate(25); 

    }else{

      return Customer::where("$type","<>", null)
      ->whereRaw("CASE WHEN $filter = 1 THEN MONTH($type) = MONTH('$todayDate') AND  DAY($type) = DAY('$todayDate') AND YEAR($type) < YEAR('$todayDate') ELSE true END")
      ->where(function($query) use($searchkey,$type){
        $query->orWhere('name','LIKE', '%'.$searchkey.'%');
        $query->orWhere("$type",'LIKE', '%'.$searchkey.'%');
        $query->orWhere('email','LIKE', '%'.$searchkey.'%');
        $query->orWhere('mobile','LIKE', '%'.$searchkey.'%');
      })
      ->paginate(25); 

    }

  }catch(Exception $e){
    Log::error("CustomersController@getBirthdayAnivarsaryData ".$e);
    return View("errors.exception");
  }

}

public function getBirthdayData($searchkey,$todayDate,$date1,$month1,$date2,$month2,$type){
  // return("hi");
    // return $customers = Customer::all()
      // return $customers = Customer::whereBetween('birthday',[$date1,$date2])->get();
 
  try
  {
    if(trim($searchkey)==''){
      
    
       return Customer::where("$type","<>", null)
      ->whereRaw("MONTH($type) >=$month1 AND DAY($type) >= $date1 AND YEAR($type) < YEAR('$todayDate')")
      ->whereRaw("MONTH($type)<=$month2 AND DAY($type) <=$date2 AND YEAR($type) < YEAR('$todayDate')")
      ->paginate(25); 
     
      
     

    }else{

      return Customer::where("$type","<>", null)
      ->whereRaw("MONTH($type) >=$month1 AND DAY($type) >= $date1 AND YEAR($type) < YEAR('$todayDate')")
      ->whereRaw("MONTH($type)<=$month2 AND DAY($type) <=$date2 AND YEAR($type) < YEAR('$todayDate')")
      ->where(function($query) use($searchkey,$type){
        $query->orWhere('name','LIKE', '%'.$searchkey.'%');
        $query->orWhere("$type",'LIKE', '%'.$searchkey.'%');
        $query->orWhere('email','LIKE', '%'.$searchkey.'%');
        $query->orWhere('mobile','LIKE', '%'.$searchkey.'%');
      })
      ->paginate(25); 

    }

  }
  catch(Exception $e){
     Log::error("CustomersController@getBirthdayData ".$e);
    return View("errors.exception");
  }
}

}
