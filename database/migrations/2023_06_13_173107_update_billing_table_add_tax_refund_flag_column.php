<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBillingTableAddTaxRefundFlagColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("billings", function(Blueprint $table){
           
            $table->integer("tax_refund_flag")->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("billings", function(Blueprint $table){
            $table->dropColumn('tax_refund_flag');
            
            });
    }
}
