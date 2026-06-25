<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {

            $table->increments('id');
            $table->string('productCode')->nullable();
            $table->integer('category')->unsigned();
            $table->string('barcode');
            $table->string('itemName');
            $table->string('itemNameArabic')->nullable();
            $table->float('costPrise');
            $table->float('salePrise');
            $table->longText('description')->nullable();
            $table->string('unit'); 
            $table->double('stock'); 
            $table->integer('min')->nullable(); 
            $table->integer('max')->nullable(); 
            $table->date("expiry_date")->nullable();
            $table->string("image")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
