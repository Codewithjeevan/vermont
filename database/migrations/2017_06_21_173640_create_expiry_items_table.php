<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpiryItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expiry_items', function (Blueprint $table) {
            $table->integer('expiry_order_id')->unsigned();
           // $table->integer('mrn_no');
            $table->integer('item_id')->unsigned();
            $table->integer('quantity');
            $table->double('rate');
            $table->double('amt');
            $table->string('remark'); 
        });
         Schema::table('expiry_items', function($table){
             $table->foreign('expiry_order_id')->references('id')->on('damage_stocks')->onDelete('cascade');
        });
          Schema::table('expiry_items', function($table){
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
         Schema::table('expiry_items', function($table) {
        $table->dropForeign(['expiry_order_id']);
        $table->dropForeign(['item_id']);

      });
        Schema::dropIfExists('expiry_items');
    }
}
