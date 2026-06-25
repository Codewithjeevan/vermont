<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->date('payment_date');
            $table->integer("supplier_id")->unsigned();
            $table->string("bill_no");
            $table->string("mode");
            $table->integer("bank_id")->nullable();
            $table->string("ch_no")->nullable();    
            $table->date("ch_date")->nullable();
            $table->double("paid_amount");
            $table->longText("remark")->nullable();
            $table->timestamps();
        });

          Schema::table('payments', function($table){
             $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('payments', function($table) {
        $table->dropForeign(['supplier_id']);
       });
        Schema::dropIfExists('payments');
    }
}
