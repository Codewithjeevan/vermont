<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasedItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchased_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("purchase_id")->unsigned(); 
            $table->integer("item_id")->unsigned();
            $table->double("qty");
            $table->string("unit");
            $table->double("costPrise");
            $table->date("expiryDate")->nullable();
            $table->double("discount")->nullable();
            $table->double("totalDiscount")->nullable();
            
            $table->double("amount");

            
            $table->timestamps();
        });

          Schema::table('purchased_items', function($table){
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
        });

         Schema::table('purchased_items', function($table){
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
        Schema::table('purchased_items', function($table) {
        $table->dropForeign(['purchase_id']);
        $table->dropForeign(['item_id']);
       });

        Schema::dropIfExists('purchased_items');
    }
}
