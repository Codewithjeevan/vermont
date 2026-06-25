<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClubBarcodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_barcodes', function (Blueprint $table) { 
            $table->increments('id');
            $table->integer('item_id')->unsigned();
            $table->double('quantity');
            $table->string('size');
            $table->timestamps();

        });
        Schema::table('club_barcodes', function($table){
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
         Schema::table('club_barcodes', function($table) {
        $table->dropForeign(['item_id']);
       });
        Schema::dropIfExists('club_barcodes');
    }
}
