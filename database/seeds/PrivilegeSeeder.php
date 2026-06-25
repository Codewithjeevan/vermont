<?php

use Illuminate\Database\Seeder;

class PrivilegeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$resources = DB::table('resources')->get();
    	$privileges = array();
    	foreach ($resources as $r) {
    		if($r->slug == "resource" || $r->slug == "role" || $r->slug == "privileges"){
    			array_push($privileges ,array(
         			"roleId" => 2,
         			"resourceId" => $r->id,
         			"view" =>  0 ,
         			"create" =>  0,
         			"update" =>  0,
         			"delete" => 0
         			));
    		}else{
    			array_push($privileges ,array(
         			"roleId" => 2,
         			"resourceId" => $r->id,
         			"view" =>  1 ,
         			"create" =>  1,
         			"update" =>  1,
         			"delete" => 1
         			));
    		}
    		array_push($privileges ,array(
         			"roleId" => 1,
         			"resourceId" => $r->id,
         			"view" =>  1 ,
         			"create" =>  1,
         			"update" =>  1,
         			"delete" => 1
         			));
    	}
        DB::table('privileges')->insert($privileges);

    }
}
