<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBillReturnItemsTableAddVatColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_return_items', function($table) {
            $table->double("vat")
            ->default(0)
            ->comment("%")
            ->after('rate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_return_items', function($table) {
            $table->dropColumn("vat");
        });
    }
}
