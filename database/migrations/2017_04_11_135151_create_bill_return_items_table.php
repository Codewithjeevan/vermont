<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillReturnItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_return_items', function (Blueprint $table) {
            $table->integer('return_id');
            $table->integer('item_id');
            $table->string('serial_no')->nullable();
            $table->double('qty');
            $table->boolean('offer_type')->comment("1. package 2.X Y promotion");
            $table->double('rate')->comment("sale Prise");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bill_return_items');
    }
}
