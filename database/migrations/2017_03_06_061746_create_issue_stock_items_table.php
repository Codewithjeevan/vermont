<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIssueStockItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issue_stock_items', function (Blueprint $table) {

           $table->integer('issue_stocks_id')->unsigned();
           $table->integer('item_id')->unsigned();
           $table->double('quantity');
           $table->double('rate');
            $table->double('amt');
           $table->string('remark')->nullable(); 
       });
        Schema::table('issue_stock_items', function($table){
            $table->foreign('issue_stocks_id')->references('id')->on('issue_stocks')->onDelete('cascade');
        });
            Schema::table('issue_stock_items', function($table){
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
      Schema::table('issue_stock_items', function($table) {
        $table->dropForeign(['issue_stocks_id']);
        $table->dropForeign(['item_id']);
       });
        Schema::dropIfExists('issue_stock_items');
    }
}
