<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	
    	DB::table('users')->insert(array(
    		array(
    			"name" => "Superadmin",
    			"email" => "Superadmin",
    			"Password" => bcrypt("admin@123"),
    			"created_at" => Carbon::now(),
    			"updated_at" => Carbon::now(),
    			"roleId"	=> 1,
    			"Floor"	=> 'Floor 1'
    			),
                array(
                    "name" => "admin",
                    "email" => "admin",
                    "Password" => bcrypt("admin@123"),
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now(),
                    "roleId"    => 2,
                    "Floor" => 'Floor 1'
                )
    		));
    }
}
