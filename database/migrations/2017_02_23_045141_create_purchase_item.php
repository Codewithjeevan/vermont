<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->integer('purchase_order_id')->unsigned();
            $table->integer('item_id')->unsigned();
            $table->integer("unit")->unsigned();
            $table->double("cost_price");
            $table->double('quantity');
        });
           Schema::table('purchase_order_items', function($table){
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
        });

         Schema::table('purchase_order_items', function($table){
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
        });
         Schema::table('purchase_order_items', function($table){
            $table->foreign('unit')->references('id')->on('units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
          Schema::table('purchase_order_items', function($table) {
        $table->dropForeign(['purchase_order_id']);
        $table->dropForeign(['item_id']);
        $table->dropForeign(['unit']);
       });
       Schema::dropIfExists('purchase_order_items');
   }
}
