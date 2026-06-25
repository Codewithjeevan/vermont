<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCpVatInPurchaseReturnItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('purchase_return_items', function($table) {
            $table->double("cp_vat")
           ->default(0)
           ->comment("%")
            ->after('costPrise');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('purchase_return_items', function($table) {
         $table->dropColumn("cp_vat");
        });
    }
}
