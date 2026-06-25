<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOpeningCashesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opening_cashes', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime("open_cash_date");
            $table->double("opening_amt");
            $table->integer("user_id")->unsigned();
            $table->timestamps();
        });
         Schema::table('opening_cashes', function($table){
             $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('opening_cashes', function($table) {
        $table->dropForeign(['user_id']);
       });
        Schema::dropIfExists('opening_cashes');
    }
}
