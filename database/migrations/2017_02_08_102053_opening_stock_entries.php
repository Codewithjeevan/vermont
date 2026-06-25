<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OpeningStockEntries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('opening_stock', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime("open_stock_date");  
            $table->integer("itemId")->unsigned();
            $table->double("openingStock");
            $table->integer("unit");
            $table->timestamps();
        });

        //   Schema::table('opening_stock', function($table){
        //     $table->foreign('itemId')->references('id')->on('items')->onDelete('cascade');
        // });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //   Schema::table('opening_stock', function($table) {
        // $table->dropForeign(['itemId']);
        //  });
        Schema::dropIfExists('opening_stock');
    }
}
