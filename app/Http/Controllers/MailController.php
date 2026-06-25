<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use Illuminate\Support\Facades\Input;

use App\Supplier;
use App\Item;
use App\PurchaseOrder;
use App\PurchaseOrderItem;
use App\Category;
use App\Customer;
use Toastr;
use PDF;
use Carbon\Carbon;
use DB;
use Common;
use Storage;

use Exception;
use Log;



class MailController extends Controller
{ 
  private $optionEmail;
  Private $currancy;
  private $rDateFormat;
  private $rDateFormatSQL;

  public function __construct()
  { 
    $this->optionEmail= Common::email(); 
    $this->currancy=Common::curr();
    $this->rDateFormat = Common::rDateFormat();
    $this->rDateFormatSQL = Common::rDateFormatSQL($this->rDateFormat);
    
  }
    /**
     * created for send anniversary mail with attachmnet 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */ 
    public function aniversarysendMail(Request $request)
    {       

      try{
        $emailArray = $request->all()["data"];      
        $subject = $emailArray['subjectData'];
        $message =  $emailArray['messageData'];
        $mallDetails = $this->mallDetails();

        if(sizeof($emailArray['emailData']) >0){     
          foreach ($emailArray['emailData'][0] as $row) {

            $custDetails = Customer::where('email', $row)->first();          
            $data = array('name'=> $custDetails -> name,'messageBody' => $message);
            $to = $row;
            $mallDetails['name']=$custDetails->name;
            $mallDetails['messageBody']=$message;
            Mail::send('pages.reports.anniversary_list.anniversary_mail', $mallDetails , function($message) use ($to,$subject){            
              $message->to($to, 'Customer Name')->subject
              ($subject);
              $message->from($this->optionEmail,'Retail Admin');          
            });
          }
          $response["status"] ="success";
          $response["message"] = trans("validation.success_mail",[ "attribute" => trans('glob.email') ]);
          return Response($response, 200);
        }else{
          $response["status"] ="error";
          $response["message"] = trans("validation.error_mail",[ "attribute" => trans('glob.email') ]);
          return Response($response, 200);
        }

      }catch(Exception $e){
        Log::error("MailController@aniversarysendMail ".$e);
        return View("errors.exception");
      }

    }

/**
     * created for send birthday mail with attachmnet 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */ 
public function birthdaysendMail(Request $request)
{
  try{
    $emailArray = $request->all()["data"];      
    $subject = $emailArray['subjectData'];
    $message =  $emailArray['messageData'];
    $mallDetails = $this->mallDetails();

    if(sizeof($emailArray['emailData']) >0){     
      foreach ($emailArray['emailData'][0] as $row) {

        $custDetails = Customer::where('email', $row)->first();          
        $data = array('name'=> $custDetails -> name,'messageBody' => $message);
        $to = $row;
        $mallDetails['name']=$custDetails->name;
        $mallDetails['messageBody']=$message;
        Mail::send('pages.reports.birthday_list.birthday_mail', $mallDetails , function($message) use ($to,$subject){               
         $message->to($to, 'Customer Name')->subject
         ($subject);
         $message->from($this->optionEmail,'Retail Admin');

       });
      }
      $response["status"] ="success";
      $response["message"] = trans("validation.success_mail",[ "attribute" => trans('glob.email') ]);
      return Response($response, 200);
    }else{
      $response["status"] ="error";
      $response["message"] = trans("validation.error_mail",[ "attribute" => trans('glob.email') ]);
      return Response($response, 200);
    }

  }catch(Exception $e){
    Log::error("MailController@birthdaysendMail ".$e);
    return View("errors.exception");
  }

}

/**
 * created for send Purchase Order mail with purchased item list 
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
public function purchaseOrdersendMail( $id)
{ 
  try{
    $mallDetails = $this->mallDetails();
    $orderList = PurchaseOrder::with(['orderItemList','orderItemList.items','orderItemList.items.categories','orderItemList.items.units' ])->find($id); 


    $itemList = array();
    if(isset($orderList->_supplier->email)){
      $to = (isset($orderList->_supplier->email)) ? $orderList->_supplier->email : '';
      $name = (isset($orderList->_supplier->name)) ? $orderList->_supplier->name : '';
      $data = array('name'=> $name ,'date' => $orderList->purchase_order_date ,'itemList' => $orderList->orderItemList,'i' => '1',  );

      $mallDetails['name']=$name;
      $mallDetails['date']=$orderList->purchase_order_date;
      $mallDetails['itemList']=$orderList->orderItemList;
      $mallDetails['i']='1';

      $pdf = PDF::loadView('pages.stock.purchase_order.purchase_order_pdf', [
        'name'=> $name ,
        'date' => (new Carbon($orderList->purchase_order_date))->format($this->rDateFormat),
        'itemList' => $orderList->orderItemList,
        'po_no'=> (isset($orderList->_purchase->po_no)) ? 'Completed' : 'Pending',
        'po_id' => $orderList->id,
        'i' => '1'
        ]);


      Mail::send('pages.stock.purchase_order.puchase_order_mail', $mallDetails , function($message) use ($to,$name,$pdf){              
       $message->to($to, $name)->subject
       ('New Purchase Order.');
       $message->from($this->optionEmail,'Retail Admin');
       $message->attachData($pdf->output(), 'PurchaseOrder.pdf');
     });

      $response["status"] ="success";
      $response["message"] = trans("validation.success_mail",[ "attribute" => trans('glob.email') ]);
      return Response($response, 200);
    }else{
      $response["status"] ="warning";
      $response["message"] = "Please Update Supplier with valid Email !";
      return Response($response, 200);
    }

  }catch(Exception $e){
    Log::error("MailController@purchaseOrdersendMail ".$e);
    return View("errors.exception");
  }

}
/**
 * created for send general mail to customer 
 * @param  Request $request [description]
 * @return [type]           [description]
 */
public function customersendMail(Request $request)
{
  try{
    $emailArray = $request->all()["data"];      
    $subject = $emailArray['subjectData'];
    $message =  $emailArray['messageData'];
    $mallDetails = $this->mallDetails();

    if(sizeof($emailArray['emailData']) >0){     
      foreach ($emailArray['emailData'][0] as $row) {

        $custDetails = Customer::where('email', $row)->first();          
        $data = array('name'=> $custDetails -> name,'messageBody' => $message);
        $to = $row;
        $mallDetails['name']=$custDetails->name;
        $mallDetails['messageBody']=$message;
        Mail::send('pages.reports.customer_list.customer_mail', $mallDetails , function($message) use ($to,$subject){              

          $message->to($to, 'Customer Name')->subject
          ($subject);
          $message->from($this->optionEmail,'Retail Admin');

        });
      }
      $response["status"] ="success";
      $response["message"] = trans("validation.success_mail",[ "attribute" => trans('glob.email') ]);
      return Response($response, 200);
    }else{
      $response["status"] ="error";
      $response["message"] = trans("validation.error_mail",[ "attribute" => trans('glob.email') ]);
      return Response($response, 200);
    }

  }catch(Exception $e){
    Log::error("MailController@customersendMail ".$e);
    return View("errors.exception");
  }
}

/**
     * created for send ZSales Report in mail with attachment 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */ 
public function sendMailZ(Request $request)
{

  try{
    $emailArray = $request->all()["data"]; 
    $data = [];
    $mallDetails = $this->mallDetails();

    if(sizeof($emailArray) >0){

      $pdf = PDF::loadView('pages.billing.z_sales_mail', [
        "purchases" => isset($emailArray['billDetails']) ? $emailArray['billDetails'] : [],
        "openingCash" =>$emailArray['openingCash'],  
        "saleReturn"  =>$emailArray['saleReturn'],
        "billAmt" =>$emailArray['billAmt'],
        "cashAmt" =>$emailArray['cashAmt'],
        "cardAmt" =>$emailArray['cardAmt'],
        "saleCredit"  => $emailArray['saleCredit'],
        "fromDate" => $emailArray['last_day_off'],
        "toDate" => $emailArray['ztoDatePrint'],
        "i" =>'1',
        "curr"=> $this->currancy ]);

      Mail::send('pages.billing.z_sales_mail_template', $mallDetails , function($message) use ($pdf){              
       $message->to($this->optionEmail, 'Retail Admin')->subject
       ('Todays ZSalesReport.');
       $message->from($this->optionEmail,'Retail Admin');
       $message->attachData($pdf->output(), 'ZSalesReport.pdf');
     });

      $response["status"] ="success";
      $response["message"] = trans("validation.success_mail",[ "attribute" => trans('glob.email') ]);
      return Response($response, 200);
    }else{
      $response["status"] ="error";
      $response["message"] = trans("validation.error_mail",[ "attribute" => trans('glob.email') ]);
      return Response($response, 200);
    }

  }catch(Exception $e){
    Log::error("MailController@sendMailZ ".$e);
    return View("errors.exception");
  }

} 
/**
     * created for send ZSales Quantitywise Report in mail with attachment 
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */ 
public function sendMailZQty(Request $request)
{

  try{
    $emailArray = $request->all()["data"]; 
    $data = [];
    $mallDetails = $this->mallDetails();
    if(sizeof($emailArray['billDetails']) >0){  

      $pdf = PDF::loadView('pages.billing.z_qty_sales_mail', [
        "purchases" =>$emailArray['billDetails'],       
        "fromDate" => $emailArray['last_day_off'], 
        "toDate" => $emailArray['ztoDatePrint'], 
        "i" =>'1'
        ]);


      Mail::send('pages.billing.z_qty_sales_mail_template', $mallDetails , function($message) use ($pdf){              
       $message->to($this->optionEmail, 'Retail Admin')->subject
       ('Todays ZQtySalesReport.');
       $message->from($this->optionEmail,'Retail Admin');
       $message->attachData($pdf->output(), 'ZQtySalesReport.pdf');
     });

      $response["status"] ="success";
      $response["message"] = trans("validation.success_mail",[ "attribute" => trans('glob.email') ]);
      return Response($response, 200);
    }else{
      $response["status"] ="error";
      $response["message"] = trans("validation.error_mail",[ "attribute" => trans('glob.email') ]);
      return Response($response, 200);
    }

  }catch(Exception $e){
    Log::error("MailController@sendMailZQty ".$e);
    return View("errors.exception");
  }

}
/**
     * created for send XSales Report in mail with attachment 
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */ 
public function sendMailX(Request $request)
{

  try{
   $emailArray = $request->all()["data"]; 
   $data = [];
   $mallDetails = $this->mallDetails();
   if(sizeof($emailArray['billDetails']) >0){      

    $pdf = PDF::loadView('pages.billing.x_sales_mail', [
      "purchases" =>$emailArray['billDetails'],
      "openingCash" =>$emailArray['openingCash'],  
      "saleReturn"  =>$emailArray['saleReturn'],
      "billAmt" =>$emailArray['billAmt'],
      "cashAmt" =>$emailArray['cashAmt'],
      "cardAmt" =>$emailArray['cardAmt'],
      "saleCredit"  =>$emailArray['saleCredit'],
      "fromDate" => (new Carbon($emailArray['fromDate']))->format($this->rDateFormat.' h:i A'),
      "toDate" => (new Carbon($emailArray['toDate']))->format($this->rDateFormat.' h:i A'),"i" =>'1',
      "curr"=>$this->currancy
      ]);


    Mail::send('pages.billing.x_sales_mail_template', $mallDetails , function($message) use ($pdf){              
     $message->to($this->optionEmail, 'Retail Admin')->subject
     ('Todays XSalesReport.');
     $message->from($this->optionEmail,'Retail Admin');
     $message->attachData($pdf->output(), 'XSalesReport.pdf');
   });

    $response["status"] ="success";
    $response["message"] = trans("validation.success_mail",[ "attribute" => trans('glob.email') ]);
    return Response($response, 200);
  }else{
    $response["status"] ="error";
    $response["message"] = trans("validation.error_mail",[ "attribute" => trans('glob.email') ]);
    return Response($response, 200);
  }

}catch(Exception $e){
  Log::error("MailController@sendMailX ".$e);
  return View("errors.exception");
}

}
/**
     * created for send Quantity wise Sales Report in mail with attachment 
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */ 
public function sendMailQtySalesInches(Request $request)
{

  try{
   $emailArray = $request->all()["data"]; 
   $data = [];
   $mallDetails = $this->mallDetails();
   if(sizeof($emailArray['purchases']) >0){  

    $pdf = PDF::loadView('pages.sales_reports.quantity_sales_inches.quantity_sales_inches_mail', [
      "purchases" =>$emailArray['purchases'],       
      "fromDate" => $emailArray['fromDatePrint'],
      "toDate" =>$emailArray['toDatePrint'],
      "i" =>'1'
      ]);


    Mail::send('pages.sales_reports.quantity_sales_inches.quantity_sales_inches_mail_template', $mallDetails , function($message) use ($pdf){              
     $message->to($this->optionEmail, 'Retail Admin')->subject
     ('QtySalesInches Report.');
     $message->from($this->optionEmail,'Retail Admin');
     $message->attachData($pdf->output(), 'QtySalesInches.pdf');
   });

    $response["status"] ="success";
    $response["message"] = trans("validation.success_mail",[ "attribute" => trans('glob.email') ]);
    return Response($response, 200);
  }else{
    $response["status"] ="error";
    $response["message"] = trans("validation.error_mail",[ "attribute" => trans('glob.email') ]);
    return Response($response, 200);
  }

}catch(Exception $e){
  Log::error("MailController@sendMailQtySalesInches ".$e);
  return View("errors.exception");
}

}
/**
     * created for send XSales Report in mail with attachment 
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */ 
public function sendMailSalesX(Request $request)
{
  try{
    $emailArray = $request->all()["data"]; 
    $data = [];
    $mallDetails = $this->mallDetails();
    if(sizeof($emailArray['purchases']) >0){      

      $pdf = PDF::loadView('pages.sales_reports.x_sales.x_sales_mail', [
        "purchases" =>$emailArray['purchases'],
        "openingCash" =>$emailArray['openingCash'],  
        "saleReturn"  =>$emailArray['saleReturn'],
        "billAmt" =>$emailArray['billAmt'],
        "cashAmt" =>$emailArray['cashAmt'],
        "cardAmt" =>$emailArray['cardAmt'],
        "saleCredit"  =>$emailArray['saleCredit'],
        "fromDate" => $emailArray['fromDatePrint'],
        "toDate" =>$emailArray['toDatePrint'],
        "i" =>'1',
        "curr"=>$this->currancy
        ]);


      Mail::send('pages.sales_reports.x_sales.x_sales_mail_template', $mallDetails , function($message) use ($pdf){              
       $message->to($this->optionEmail, 'Retail Admin')->subject
       ('Todays XSalesReport.');
       $message->from($this->optionEmail,'Retail Admin');
       $message->attachData($pdf->output(), 'XSalesReport.pdf');
     });

      $response["status"] ="success";
      $response["message"] = trans("validation.success_mail",[ "attribute" => trans('glob.email') ]);
      return Response($response, 200);
    }else{
      $response["status"] ="error";
      $response["message"] = trans("validation.error_mail",[ "attribute" => trans('glob.email') ]);
      return Response($response, 200);
    }

  }catch(Exception $e){
    Log::error("MailController@sendMailSalesX ".$e);
    return View("errors.exception");
  }

}

/**
     * Created for send birthday and anniversary message to customer
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */ 
public function sendSMS(Request $request)
{      

  try{
    $smsArray = $request->all()["data"];      
    $subject = $smsArray['subjectData'];
    $message =  $smsArray['messageData'];

    if(sizeof($smsArray['numberData']) >0){     
      foreach ($smsArray['numberData'][0] as $row) {
        $customers = Customer::where("id", "=", $row)->get();

        $url ="http://mshastra.com/sendurlcomma.aspx?user=20074098&pwd=vermont@12345&senderid=VERMOUNTTECH&mobileno='".$customers[0]->mobile."'&msgtext='".$message."'&CountryCode=ALL";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $curl_scraped_page = curl_exec($ch);
        curl_close($ch); 

      }
      $response["status"] ="success";
      $response["message"] = trans("validation.success_mail",[ "attribute" => trans('glob.sms') ]);
      return Response($response, 200);
    }else{
      $response["status"] ="error";
      $response["message"] = trans("validation.error_mail",[ "attribute" => trans('glob.sms') ]);
      return Response($response, 200);
    }

  }catch(Exception $e){
    Log::error("MailController@sendSMS ".$e);
    return View("errors.exception");
  }

}

   /**
     * Created for send billing message to customer
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */ 
   public function sendBillingSMS(Request $request)
   {      

    try{
      $smsArray = $request->all()["data"];      

      $message = 'Dear Customer '.$smsArray['customerName'].'Thank You for shopping.';
      $url ="http://mshastra.com/sendurlcomma.aspx?user=20074098&pwd=vermont@12345&senderid=VERMOUNTTECH&mobileno='".$smsArray['customerNumber']."'&msgtext='".$message."'&CountryCode=ALL";
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $curl_scraped_page = curl_exec($ch);
      curl_close($ch);  

    }catch(Exception $e){
      Log::error("MailController@sendBillingSMS ".$e);
      return View("errors.exception");
    }       
    
  }

  /**
   * created for get shop details which is used in mail attachment
   * @return [type] [description]
   */
  public function mallDetails(){
    try{
      $MallAddress ="NA";
      $MallName ="NA";
      $MAllLogo ="NA";
      $address = DB::select("SELECT options.value as address FROM options where options.type = 'general' and options.key = 'register_address'");
      $mallName = DB::select("SELECT options.value as mallName FROM options where options.type = 'general' and options.key = 'mall_name'");
      $logo = DB::select("SELECT options.value as logo FROM options where options.type = 'general' and options.key = 'logo'");
      $telephone = DB::select("SELECT options.value as telephone FROM options where options.type = 'general' and options.key = 'mall_telephone'");   
      $mall_website = DB::select("SELECT options.value as mall_website FROM options where options.type = 'general' and options.key = 'mall_website'");



      return $res = array(      
       "address"  => (isset($address[0]->address)) ? $address[0]->address : '',   
       "mallName"  => (isset($mallName[0]->mallName)) ? $mallName[0]->mallName : '', 
       "logo"  => (isset($logo[0]->logo)) ? $logo[0]->logo : '',  
       "telephone"  => (isset($telephone[0]->telephone)) ? $telephone[0]->telephone : '', 
       "mall_website"  => (isset($mall_website[0]->mall_website)) ? $mall_website[0]->mall_website : '',   
       );

    }catch(Exception $e){
      Log::error("MailController@mallDetails ".$e);
      return View("errors.exception");
    }  
  }

  /**
     * created for send mail Updated Setting Option 
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */ 
  public function sendEmailSettingOption(Request $request){
    try{
      $emailArray=$request->all()["data"];       
      $to = 'retailsalesac@gmail.com';
      if(sizeof($emailArray) >0){
        $optionDetails= array(      
         "client_name" => $emailArray['client_name'],
         "curr"  => $emailArray['curr'],
         "textCurrency"=>$emailArray['textCurrency'],
         "textCurrencyAfterDecimal"=>$emailArray['textCurrencyAfterDecimal'],
         "amount_decimal"=>$emailArray['amount_decimal'],
         "quantity_decimal"=>$emailArray['quantity_decimal'],
         "bill_pad"=>$emailArray['bill_pad'],
         "bill_prefix_word"=>$emailArray['bill_prefix_word'],
         "email"=>$emailArray['email'],
         "barcode_type"=>$emailArray['barcode_type'],
         "barcode_weight_prefix"=>$emailArray['barcode_weight_prefix'],
         "barcode_rate_prefix"=>$emailArray['barcode_rate_prefix'],
         "barcode_decimal_price_limit"=>$emailArray['barcode_decimal_price_limit'],
         "reports_date_format"=>$emailArray['reports_date_format'],
         "date"=> Carbon::now(),
         );

        $string="\r\n"."Update Setting Option Date=".Carbon::now(). "\r\n"."Client Name=".$emailArray['client_name']. "\r\n". "Currency=".$emailArray['curr']. "\r\n". "Currency Text=".$emailArray['textCurrency']. "\r\n". "Text Currency After Decimal=".$emailArray['textCurrencyAfterDecimal']. "\r\n". "Amount Decimal=".$emailArray['amount_decimal']. "\r\n". "Quantity Decimal=".$emailArray['quantity_decimal']  ."\r\n"."Bill Number Padding=".$emailArray['bill_pad']
        ."\r\n". "Bill Prefix Word=".$emailArray['bill_prefix_word']. "\r\n". "email=".$emailArray['email']. "\r\n". "Barcode Type=".$emailArray['barcode_type']. "\r\n". "Barcode Rate prefix=".$emailArray['barcode_rate_prefix']. "\r\n". "Barcode weight Prefix=".$emailArray['barcode_weight_prefix']. "\r\n". "Barcode Decimal Price Limit=".$emailArray['barcode_decimal_price_limit']. "\r\n". "Date Format=".$emailArray['reports_date_format'];
        $client_name=$emailArray['client_name'];
        $subject='Updated option'.' '. $client_name;
        $date=Carbon::now()->format('d-m-Y');
        $filename='/mailLogs/SettingOption-'.$date.'.log';
        Storage::append($filename, $string);
        Mail::send('pages.options.setting-option-mail-template',$optionDetails, function($message) use ($to,$subject){
         $message->to($to, 'Retail Admin')->subject
         ($subject);
         $message->from($to,'Retail Admin');
       });
        $response["status"] ="success";
        $response["message"] = trans("validation.success_mail",[ "attribute" => trans('glob.email') ]);
        return Response($response, 200);
      }
      else{
        $response["status"] ="error";
        $response["message"] = trans("validation.error_mail",[ "attribute" => trans('glob.email') ]);
        return Response($response, 200);
      }

    }catch(Exception $e){
      Log::error("MailController@sendEmailSettingOption ".$e);
      return View("errors.exception");
    } 

  }
}
