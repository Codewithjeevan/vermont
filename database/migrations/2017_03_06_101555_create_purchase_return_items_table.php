<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseReturnItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_return_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("purchase_return_id")->unsigned();
            $table->integer("item_id")->unsigned();
           // $table->double("mrp");
            $table->double("costPrise");
            $table->double("qty");
            $table->double("unit");
            $table->double("discount");
            $table->double("totalDiscount");
            $table->double("amount");
           
        });
        Schema::table('purchase_return_items', function($table){
            $table->foreign('purchase_return_id')->references('id')->on('purchase_returns')->onDelete('cascade');
        });
        Schema::table('purchase_return_items', function($table){
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
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
        $table->dropForeign(['purchase_return_id']);
         $table->dropForeign(['item_id']);
       });
        Schema::dropIfExists('purchase_return_items');
    }
}
