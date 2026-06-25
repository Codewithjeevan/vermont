<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateXYPromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('x_y_promotions', function (Blueprint $table) {
            $table->integer('x_item_id')->unsigned();
            $table->double('x_quantity');
            $table->integer('y_item_id')->unsigned();
            $table->double('y_quantity');
            $table->timestamp("prmo_from");
            $table->timestamp("prmo_to");

        });

         Schema::table('x_y_promotions', function($table){
            $table->foreign('x_item_id')->references('id')->on('items')->onDelete('cascade');
        });
           Schema::table('x_y_promotions', function($table){
             $table->foreign('y_item_id')->references('id')->on('items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('x_y_promotions', function($table) {
        $table->dropForeign(['x_item_id']);
         $table->dropForeign(['y_item_id']);
       });
        Schema::dropIfExists('x_y_promotions');
    }
}
