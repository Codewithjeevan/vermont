<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->date("purchase_order_date");
             $table->integer('supplier_id')->unsigned();
            $table->timestamps();
        });

         Schema::table('purchase_orders', function($table){
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('purchase_orders', function($table) {
        $table->dropForeign(['supplier_id']);
       });
        Schema::dropIfExists('purchase_orders');
    }
}
