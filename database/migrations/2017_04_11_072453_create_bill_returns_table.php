<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_returns', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("billing_id")->nullable()->unsigned();
            $table->double("amount");
            $table->timestamps();
        });
          Schema::table('bill_returns', function($table){
             $table->foreign('billing_id')->references('id')->on('billings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('bill_returns', function($table) {
            $table->dropForeign(['billing_id']);
              
        });
        Schema::dropIfExists('bill_returns');
    }
}
