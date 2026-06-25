<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->increments('id');
            $table->date('return_date');
            $table->integer('supplier_id')->unsigned();
            $table->string('supplier_billno');
            $table->string('return_through');
            $table->double('subTotal');
            $table->double('disAmount');
            $table->double('extraDiscount');
            $table->double('billAmount');
            $table->timestamps();
        });

         Schema::table('purchase_returns', function($table){
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
        Schema::table('purchase_returns', function($table) {
        $table->dropForeign(['supplier_id']);
       });
        Schema::dropIfExists('purchase_returns');
    }
}
