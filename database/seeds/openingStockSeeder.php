<?php

use Illuminate\Database\Seeder;

class openingStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\OpeningStock::class,50)->create();
    }
}
