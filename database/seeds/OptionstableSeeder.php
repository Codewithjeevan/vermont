<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OptionstableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('options')->insert(array(

    		/** seeder for general options  */
    		array("type" => "general","key" => "change_com_port","value" => 0,"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
    		array("type" => "general","key" => "change_sms_message","value" => '',"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
    		array("type" => "general","key" => "enable_sms_billing","value" => 0,"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
    		array("type" => "general","key" => "register_address","value" => '',"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
    		array("type" => "general","key" => "mall_name","value" => '',"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
    		array("type" => "general","key" => "logo","value" => '/img/default_logo.png',"created_at" => Carbon::now(),"updated_at" => Carbon::now()),

    		/** seeder for billing options */
    		
    		array("type" => "billing","key" => "hold_bill","value" => 1,"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
    		array("type" => "billing","key" => "unhold_bill","value" => 0,"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
    		array("type" => "billing","key" => "retail_invoice","value" => 0,"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
    		array("type" => "billing","key" => "serial_number","value" => 0,"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
    		array("type" => "billing","key" => "barcode_add","value" => 1,"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
    		array("type" => "billing","key" => "modify_qty","value" => 0,"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
    		array("type" => "billing","key" => "client_buying_history","value" => 1,"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
    		array("type" => "billing","key" => "open_cash_drawer","value" => 0,"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
    		array("type" => "billing","key" => "customer_details","value" => 1,"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
    		array("type" => "billing","key" => "search_items","value" => 1,"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
    		array("type" => "billing","key" => "voucher_issue","value" => 0,"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
    		array("type" => "billing","key" => "voucher_redeem","value" => 0,"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
    		array("type" => "billing","key" => "calc","value" => 1,"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
    		array("type" => "billing","key" => "home_delivery","value" => 1,"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
    		array("type" => "billing","key" => "sale_return","value" => 1,"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
    		array("type" => "billing","key" => "reprint_sale_return_receipt","value" => 1,"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
    		array("type" => "billing","key" => "report","value" => 1,"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
    		array("type" => "billing","key" => "receive_home_delivery_payment","value" => 1,"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
    		array("type" => "billing","key" => "gift_bill_print","value" => 0,"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
    		array("type" => "billing","key" => "billscreen","value" => 1,"created_at" => Carbon::now(),"updated_at" => Carbon::now()),

    		/** seeder for item maser options */
    		array("type" => "item","key" => "wholesale","value" => 0,"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
    		array("type" => "item","key" => "item_with_boxes","value" => 0,"created_at" => Carbon::now(),"updated_at" => Carbon::now()),

            /** seeder for setting options */
            array("type" => "setting","key" => "curr","value" =>config("custom.curr"),"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
            array("type" => "setting","key" => "textCurrency","value" =>config("custom.text_currency"),"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
            array("type" => "setting","key" => "textCurrencyAfterDecimal","value" =>config("custom.text_currency_after_decimal"),"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
            array("type" => "setting","key" => "amount_decimal","value" =>config("custom.amt_fix"),"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
            array("type" => "setting","key" => "quantity_decimal","value" =>config("custom.qty_fix"),"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
            array("type" => "setting","key" => "bill_pad","value" =>config("custom.bill_pad"),"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
            array("type" => "setting","key" => "bill_prefix_word","value" =>config("custom.bill_prefix_word"),"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
            array("type" => "setting","key" => "email","value" =>config("custom.email"),"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
            array("type" => "setting","key" => "barcode_type","value" =>config("custom.barcode_type"),"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
            array("type" => "setting","key" => "barcode_rate_prefix","value" =>config("custom.barcode_rate_prefix"),"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
            array("type" => "setting","key" => "barcode_weight_prefix","value" =>config("custom.barcode_weight_prefix"),"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
            array("type" => "setting","key" => "barcode_decimal_price_limit","value" =>config("custom.barcode_decimal_price_limit"),"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
            array("type" => "setting","key" => "reports_date_format","value" =>config("custom.date_format"),"created_at" => Carbon::now(),"updated_at" => Carbon::now()),
            array("type" => "vat","key" => "vat_type","value" =>config("custom.vat_type"),"created_at" => Carbon::now(),"updated_at" => Carbon::now()),

        ));

}
}
