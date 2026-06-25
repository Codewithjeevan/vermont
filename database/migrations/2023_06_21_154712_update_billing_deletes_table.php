<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBillingDeletesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("billing_deletes", function(Blueprint $table){
           
            $table->string("taxRefundTagNumber")->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("billing_deletes", function(Blueprint $table){
            $table->dropColumn('taxRefundTagNumber');
            
            });
    }
}
