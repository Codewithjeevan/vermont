<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDamageStockItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('damage_stock_items', function (Blueprint $table) {

            $table->integer('damage_order_id')->unsigned();
            $table->integer('item_id')->unsigned();
            $table->double('quantity')
            ;
            $table->double('rate');
            $table->double('amt');
            $table->string('remark'); 
            });

         Schema::table('damage_stock_items', function($table){
             $table->foreign('damage_order_id')->references('id')->on('damage_stocks')->onDelete('cascade');
        });
        Schema::table('damage_stock_items', function($table){
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
        Schema::table('damage_stock_items', function($table) {
        $table->dropForeign(['damage_order_id']);
        $table->dropForeign(['item_id']);

      });
        Schema::dropIfExists('damage_stock_items');
    }
}
