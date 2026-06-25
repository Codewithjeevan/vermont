<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpiryStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expiry_stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->date("slip_date");
            $table->integer('enterd_by')->unsigned();
            $table->double('approx_value_of');
            $table->timestamps();
        });
         Schema::table('expiry_stocks', function($table){
            $table->foreign('enterd_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('expiry_stocks', function($table) {
        $table->dropForeign(['enterd_by']);
       });
        Schema::dropIfExists('expiry_stocks');
    }
}
