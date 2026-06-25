<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class categorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	App\Category::insert(array(
    		"categorie" => "General",
    		"parent_category" => 0,
    		"created_at" => Carbon::now(),
    		"updated_at" => Carbon::now()
    	));

        //factory(App\Category::class,10)->create();
    }
}
