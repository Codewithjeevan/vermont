<?php
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ResourceSeeder::class);
        $this->call(RolesSeeder::class);
        $this->call(PrivilegeSeeder::class);
        $this->call(UserSeeder::class);
        //$this->call(bankSeeder::class);
        $this->call(categorySeeder::class);
        //$this->call(customerSeeder::class);
        $this->call(UnitsSeeder::class);
        //$this->call(itemSeeder::class);
        //$this->call(openingStockSeeder::class);
        //$this->call(salesmanSeeder::class);
        //$this->call(supplierSeeder::class);
        $this->call(OptionstableSeeder::class);
        $this->call(loyaltySeeder::class);
    }
}
