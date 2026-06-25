<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_items', function (Blueprint $table) {
            $table->integer('billing_id')->unsigned();
            $table->integer('item_id');
            $table->string('serial_no')->nullable();
            $table->double('qty');
            $table->double('rate')->comment("sale Prise");
            $table->boolean('offer_type')->comment("1. package 2.X Y promotion");
            $table->boolean('is_promotion')->comment("true if timely promotion has applied");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('billing_items');
    }
}
