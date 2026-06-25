<?php
use Illuminate\Database\Seeder;
use Carbon\Carbon;
class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert(array(
        	array(
        		"id" => 1,
        		"role" => "Superadmin",
        		"created_at" => Carbon::now(),
        		"updated_at" => Carbon::now()
        		),
        	array(
        		"id" => 2,
        		"role" => "Admin",
        		"created_at" => Carbon::now(),
        		"updated_at" => Carbon::now()
        		)
        	));
    }
}
