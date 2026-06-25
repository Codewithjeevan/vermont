<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Audit;
use App\Bank;
use App\Billing;
use App\BillingDelete;
use App\BillingHomeDelivery;
use App\BillingItems;
use App\BillReturnItems;
use App\BillReturns;
use App\Category;
use App\ClubBarcode;
use App\Customer;
use App\CustomerReceipt;
use App\DamageStock;
use App\DamageStockItem;
use App\ExpiryItems;
use App\ExpiryStock;
use App\ExpiryStockItem;
use App\IssueStock;
use App\IssueStockItem;
use App\Item;
use App\Location;
use App\OpeningCash;
use App\OpeningStock;
use App\Option;
use App\Package;
use App\Payment;
use App\Privilege;
use App\Purchase;
use App\PurchasedItem;
use App\PurchaseOrder;
use App\PurchaseOrderItem;
use App\PurchaseReturn;
use App\PurchaseReturnItem;
use App\Resource;
use App\Role;
use App\Salesman;
use App\Stock;
use App\Supplier;
use App\Unit;
use App\User;
use App\XYPromotion;
use App\zSales;



class ResetApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:app {--all} {--seed}'; 

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will reset Application';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bar = $this->output->createProgressBar(34);
        if($this->option("all")){
            $bar = $this->output->createProgressBar(41);
        }
        

        if ($this->confirm('Do you wish to continue?')) {
            $this->line('------ Resetting Application This may take a while ------');
            Audit::truncate();
            $bar->advance();
            Bank::truncate();
            $bar->advance();
            Billing::truncate();
            $bar->advance();
            BillingDelete::truncate();
            $bar->advance();
            BillingHomeDelivery::truncate();
            $bar->advance();
            BillingItems::truncate();
            $bar->advance();
            BillReturnItems::truncate();
            $bar->advance();
            BillReturns::truncate();
            $bar->advance();
            if($this->option("all")){
                Category::truncate();
                $bar->advance();
            }
            
            ClubBarcode::truncate();
            $bar->advance();
            Customer::truncate();
            $bar->advance();
            CustomerReceipt::truncate();
            $bar->advance();
            DamageStock::truncate();
            $bar->advance();
            DamageStockItem::truncate();
            $bar->advance();
            ExpiryItems::truncate();
            $bar->advance();
            ExpiryStock::truncate();
            $bar->advance();
            ExpiryStockItem::truncate();
            $bar->advance();
            IssueStock::truncate();
            $bar->advance();
            IssueStockItem::truncate();
            $bar->advance();
            if($this->option("all")){
                Item::truncate();
                $bar->advance();
            }
            Location::truncate();
            $bar->advance();
            OpeningCash::truncate();
            $bar->advance();
            OpeningStock::truncate();
            $bar->advance();
            //Option::truncate();
            //$bar->advance();
            Package::truncate();
            $bar->advance();
            Payment::truncate();
            $bar->advance();
            Purchase::truncate();
            $bar->advance();
            PurchasedItem::truncate();
            $bar->advance();
            PurchaseOrder::truncate();
            $bar->advance();
            PurchaseOrderItem::truncate();
            $bar->advance();
            PurchaseReturn::truncate();
            $bar->advance();
            PurchaseReturnItem::truncate();
            $bar->advance();
            Salesman::truncate();
            $bar->advance();
            Stock::truncate();
            $bar->advance();
            Supplier::truncate();
            $bar->advance();
            if($this->option("all")){
                Resource::truncate();
                $bar->advance();
                Role::truncate();
                $bar->advance();
                Unit::truncate();
                $bar->advance();
                User::truncate();
                $bar->advance();
            }

            if($this->option("all")){
                Privilege::truncate();
                $bar->advance();
            }

            XYPromotion::truncate();
            $bar->advance();
            zSales::truncate();
            $bar->advance();
            if($this->option("seed")){
                $this->info('' . PHP_EOL);
                $this->line("Seeding Bank");
                \Artisan::call("db:seed", [
                    "--class" => "bankSeeder",
                ]);
                $this->line("Seeding Resource");
                \Artisan::call("db:seed", [
                    "--class"=> "ResourceSeeder",
                ]);
                $this->line("Seeding Roles");
                \Artisan::call("db:seed", [
                    "--class"=> "RolesSeeder",
                ]);
                $this->line("Seeding privileges");
                \Artisan::call("db:seed", [
                    "--class"=> "PrivilegeSeeder",
                ]);
                // $this->line("Seeding Branch Resources");
                // \Artisan::call("db:seed", [
                //     "--class"=> "BranchResourceSeeder",
                // ]);
                // $this->line("Seeding Branch Role");
                // \Artisan::call("db:seed", [
                //     "--class"=> "BranchRoleSeeder",
                // ]);
                // $this->line("Seeding Branch Privileges");
                // \Artisan::call("db:seed", [
                //     "--class"=> "BranchPrivilegeSeeder",
                // ]);
                $this->line("Seeding User");
                \Artisan::call("db:seed", [
                    "--class"=> "UserSeeder",
                ]);
                $this->line("Seeding Categories");
                \Artisan::call("db:seed", [
                    "--class"=> "categorySeeder",
                ]);
                $this->line("Seeding Customer");
                \Artisan::call("db:seed", [
                    "--class"=> "customerSeeder",
                ]);
                $this->line("Seeding unit");
                \Artisan::call("db:seed", [
                    "--class"=> "UnitsSeeder",
                ]);
                $this->line("Seeding Item");
                \Artisan::call("db:seed", [
                    "--class"=> "itemSeeder",
                ]);
                $this->line("Seeding OpeningStock");
                \Artisan::call("db:seed", [
                    "--class"=> "openingStockSeeder",
                ]);
                $this->line("Seeding Salesman");
                \Artisan::call("db:seed", [
                    "--class"=> "salesmanSeeder",
                ]);
                $this->line("Seeding Supplier");
                \Artisan::call("db:seed", [
                    "--class"=> "supplierSeeder",
                ]);
            }

            $this->info('' . PHP_EOL);
            $this->info(' Done. ' . PHP_EOL);
            $this->info('Application has reset successfully!');

        }else{

        }

        $bar->finish();
    }
}
