
<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
class UnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('units')->insert(array(
    		array('id' => '1','unit' => 'PC','created_at' => Carbon::now(),'updated_at' => Carbon::now()), 
    		array('id' => '2','unit' => 'M','created_at' => Carbon::now(),'updated_at' => Carbon::now()), 
    		array('id' => '3','unit' => 'Kg','created_at' => Carbon::now(),'updated_at' => Carbon::now()), 
            array('id' => '4','unit' => 'G','created_at' => Carbon::now(),'updated_at' => Carbon::now()), 
    		array('id' => '5','unit' => 'L','created_at' => Carbon::now(),'updated_at' => Carbon::now()), 
    		));
    }
}
