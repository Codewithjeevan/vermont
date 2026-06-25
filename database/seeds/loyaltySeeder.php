<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
class loyaltySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('loyalties')->insert(array(

        array('key' => 'loyalty_activation','value' => 0,'created_at' => Carbon::now(),'updated_at' => Carbon::now()),        
        array('key' => 'loyalty_points_on_sale','value' => 0,'created_at' => Carbon::now(),'updated_at' => Carbon::now()),    

        array('key' => 'loyalty_points','value' => 0,'created_at' => Carbon::now(),'updated_at' => Carbon::now()),        
        array('key' => 'loyalty_redeem_maturity','value' => 0,'created_at' => Carbon::now(),'updated_at' => Carbon::now()),        
        
        array('key' => 'loyalty_on_redeem_points','value' => 0,'created_at' => Carbon::now(),'updated_at' => Carbon::now()),        
        array('key' => 'loyalty_on_redeem_discount','value' => 0,'created_at' => Carbon::now(),'updated_at' => Carbon::now()),                
        ));
  }
}
