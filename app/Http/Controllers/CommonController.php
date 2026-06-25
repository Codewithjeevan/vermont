<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Exception;
use Log;

class CommonController extends Controller
{
    public function add($controller){
        try{
            return Redirect()->action($controller."Controller@index")->with("open","true");
        }catch(Exception $e){
            Log::error("CommonController@add ".$e);
            return View("errors.exception");
        }
    } 

  /**
   * get all setting options
   * @return [array] setting options
   */
  public function settingOption(){
    try{
        $arrcurrOption= \App\Option::where("key","=",'curr')->get();
        $strCurrOptionValue=isset($arrcurrOption[0]->value) ? $arrcurrOption[0]->value:config("custom.curr");
        $arrtextCurrOption= \App\Option::where("key","=",'textCurrency')->get();
        $strTextCurrOptionValue=isset($arrtextCurrOption[0]->value)?$arrtextCurrOption[0]->value.' ':config("custom.text_currency");
        $arrTextCurrAfterDecimalOption= \App\Option::where("key","=",'textCurrencyAfterDecimal')->get();
        $strTextCurrAfterDecimalOptionValue=isset($arrTextCurrAfterDecimalOption[0]->value)?' '.$arrTextCurrAfterDecimalOption[0]->value:config("custom.text_currency_after_decimal");
        $arrAmountFixOption= \App\Option::where("key","=",'amount_decimal')->get();
        $intAmountFixOption= isset($arrAmountFixOption[0]->value) ? $arrAmountFixOption[0]->value :config("custom.amt_fix");
        $arrQuantityFixOption= \App\Option::where("key","=",'quantity_decimal')->get();
        $intrQuantityFixOption= isset($arrQuantityFixOption[0]->value)?$arrQuantityFixOption[0]->value: config("custom.qty_fix");
        $arrBillPadOption= \App\Option::where("key","=",'bill_pad')->get();
        $intBillPadOption= isset($arrBillPadOption[0]->value)?$arrBillPadOption[0]->value:config("custom.bill_pad");
        $arrBillPadwordOption= \App\Option::where("key","=",'bill_prefix_word')->where("type","like","%".'setting'."%")->get();
        $strBillPadwordOption= isset($arrBillPadwordOption[0]->value)?$arrBillPadwordOption[0]->value:config("custom.bill_prefix_word");
        $arrEmailOption= \App\Option::where("key","=",'email')->where("type","like","%".'setting'."%")->get();
        $strEmailOption= isset($arrEmailOption[0]->value)?$arrEmailOption[0]->value:config("custom.email");
        $arrBarcodeTypeOption= \App\Option::where("key","=",'barcode_type')->where("type","like","%".'setting'."%")->get();
        $strBarcodeType= isset($arrBarcodeTypeOption[0]->value)?$arrBarcodeTypeOption[0]->value:config("custom.barcode_type");
        $arrBarcodeRatePrifixOption= \App\Option::where("key","=",'barcode_rate_prefix')->where("type","like","%".'setting'."%")->get();
        $intBarcodeRatePrifixOption=isset($arrBarcodeRatePrifixOption[0]->value)? $arrBarcodeRatePrifixOption[0]->value:config("custom.barcode_rate_prefix");
        $arrBarcodeWeightPrefixOption= \App\Option::where("key","=",'barcode_weight_prefix')->where("type","like","%".'setting'."%")->get();
        $intBarcodeWeightPrifixOption= isset($arrBarcodeWeightPrefixOption[0]->value)?$arrBarcodeWeightPrefixOption[0]->value:config("custom.barcode_weight_prefix");
        $arrBarcodeDecimalPriceLimitOption= \App\Option::where("key","=",'barcode_decimal_price_limit')->where("type","like","%".'setting'."%")->get();
        $intBarcodeDecimalPriceLimitOption=isset($arrBarcodeDecimalPriceLimitOption[0]->value)?$arrBarcodeDecimalPriceLimitOption[0]->value:config("custom.barcode_decimal_price_limit");

        $arrRDateFormatOption= \App\Option::where("key","=",'reports_date_format')->where("type","like","%".'setting'."%")->get();
        $strRDateFormatOption=isset($arrRDateFormatOption[0]->value)?$arrRDateFormatOption[0]->value:config("custom.date_format");
        $arrVatType= \App\Option::where("key","=",'vat_type')->where("type","like","%".'vat'."%")->get();
        $intVatType=isset($arrVatType[0]->value)?$arrVatType[0]->value:config("custom.vat_type");

        $res = array(
            "currency"  => $strCurrOptionValue,         
            "textCurrency" => $strTextCurrOptionValue,
            "textCurrencyAfterDecimal"=>$strTextCurrAfterDecimalOptionValue,
            "fixAmount"=>$intAmountFixOption,
            "fixQuantity"=>$intrQuantityFixOption,
            "billPadding"=>$intBillPadOption,
            "billPrefixWord"=>$strBillPadwordOption,
            "emailOption"=>$strEmailOption,
            "barcodeType"=>$strBarcodeType,
            "barcodRatePrefixNumber"=>$intBarcodeRatePrifixOption,
            "barcodeWeightPrefixNumber"=>$intBarcodeWeightPrifixOption,
            "barcodeDecimalPriceLimit"=>$intBarcodeDecimalPriceLimitOption,
            'rDateFormat'=> $strRDateFormatOption,
            'vatType'=>$intVatType
            );

        return $res;

    }catch(Exception $e){
        Log::error("CommonController@settingOption ".$e);
        return View("errors.exception");
    }

}

}
