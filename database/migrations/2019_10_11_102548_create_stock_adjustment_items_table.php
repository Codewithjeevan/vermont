<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockAdjustmentItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('stock_adjustment_items', function (Blueprint $table) {

           $table->increments('id'); 
           $table->integer('stock_adjustment_id')->unsigned();
           $table->integer('item_id')->unsigned();;
           $table->double('quantity');
           $table->double('rate');
           $table->double('amt');
           $table->integer('adjust_through');
       });
      
    
         Schema::table('stock_adjustment_items', function($table){
            $table->foreign('stock_adjustment_id')->references('id')->on('stock_adjustments')->onDelete('cascade');
        });
     Schema::table('stock_adjustment_items', function($table){
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
        Schema::dropIfExists('stock_adjustment_items');
    }
}
