<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingHomeDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_home_deliveries', function (Blueprint $table) {
            $table->integer("customer_id")->unsigned();
            $table->integer("billing_id")->unsigned();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('flat_no')->nullable();
            $table->string('blg')->nullable();
            $table->string('street')->nullable();
        });

         Schema::table('billing_home_deliveries', function($table){
             $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
          Schema::table('billing_home_deliveries', function($table){
             $table->foreign('billing_id')->references('id')->on('billings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('billing_home_deliveries', function($table) {
        $table->dropForeign(['customer_id']);
        $table->dropForeign(['billing_id']);
       });
        Schema::dropIfExists('billing_home_deliveries');
    }
}
