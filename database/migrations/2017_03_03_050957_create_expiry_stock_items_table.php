<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpiryStockItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expiry_stock_items', function (Blueprint $table) {

            $table->increments('id');            
            $table->integer('item_id')->unsigned();
            $table->double('qty');
            $table->double('rate');
            $table->date("expiry_date");
        });
        Schema::table('expiry_stock_items', function($table){
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
       Schema::table('expiry_stock_items', function($table) {
        $table->dropForeign(['item_id']);
       });
        Schema::dropIfExists('expiry_stock_items');
    }
}
