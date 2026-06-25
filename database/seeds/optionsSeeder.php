<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
class optionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('options')->insert(array(
        array('id' => '1',' type' => 'billing','key' => 'hold_bill','value' => 'false','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'master', 'parent_resource' => 0),        
        array('id' => '2',' type' => 'billing','key' => 'unhold_bill','value' => 'false','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'master', 'parent_resource' => 0),        
        array('id' => '3',' type' => 'billing','key' => 'retail_invoice','value' => 'false','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'master', 'parent_resource' => 0),        
        array('id' => '4',' type' => 'billing','key' => 'wholesale_invoice','value' => 'false','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'master', 'parent_resource' => 0),        
        
        array('id' => '5',' type' => 'billing','key' => 'modify_qty','value' => 'false','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'master', 'parent_resource' => 0),        
        array('id' => '6',' type' => 'billing','key' => 'client_buying_history','value' => 'false','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'master', 'parent_resource' => 0),        
        array('id' => '7',' type' => 'billing','key' => 'open_cash_drawer','value' => 'false','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'master', 'parent_resource' => 0),        
        array('id' => '8',' type' => 'billing','key' => 'customer_details','value' => 'false','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'master', 'parent_resource' => 0),        
        
        array('id' => '9',' type' => 'billing','key' => 'search_items','value' => 'false','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'master', 'parent_resource' => 0),        
        array('id' => '10',' type' => 'billing','key' => 'voucher_issue','value' => 'false','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'master', 'parent_resource' => 0),        
        array('id' => '11',' type' => 'billing','key' => 'voucher_redeem','value' => 'false','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'master', 'parent_resource' => 0),        
        array('id' => '12',' type' => 'billing','key' => 'calc','value' => 'false','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'master', 'parent_resource' => 0),        
        
        array('id' => '13',' type' => 'billing','key' => 'home_delivery','value' => 'false','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'master', 'parent_resource' => 0),        
        array('id' => '14',' type' => 'billing','key' => 'sale_return','value' => 'false','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'master', 'parent_resource' => 0),        
        array('id' => '15',' type' => 'billing','key' => 'reprint_sale_return_receipt','value' => 'false','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'master', 'parent_resource' => 0),        
        array('id' => '16',' type' => 'billing','key' => 'report','value' => 'false','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'master', 'parent_resource' => 0),        
        
        array('id' => '17',' type' => 'billing','key' => 'receive_home_delivery_payment','value' => 'false','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'master', 'parent_resource' => 0),        
        array('id' => '18',' type' => 'billing','key' => 'gift_bill_print','value' => 'false','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'slug' => 'master', 'parent_resource' => 0)     
        
        ));
  }
}
