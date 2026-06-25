<?php

use Illuminate\Database\Seeder;

class salesmanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Salesman::class,20)->create();
    }
}
