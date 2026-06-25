<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMrnToExpiryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('expiry_items', function (Blueprint $table) {
           $table->integer('mrn_no')
           ->after('expiry_order_id');

       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expiry_items', function(Blueprint $table)
        {
            $table->dropColumn('mrn_no');
        });
    }
}
