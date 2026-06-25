<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCpVatToPurchaseItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('purchased_items', function (Blueprint $table) {

        $table->float('cp_vat')
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
        Schema::table('purchased_items', function($table) {
            $table->dropColumn("cp_vat");
        });
    }
}
