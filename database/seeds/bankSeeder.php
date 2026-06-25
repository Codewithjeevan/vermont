<?php

use Illuminate\Database\Seeder;

class bankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Bank::class,10)->create();
    }
}
