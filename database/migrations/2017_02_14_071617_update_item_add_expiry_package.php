<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateItemAddExpiryPackage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
     Schema::table("items", function(Blueprint $table){
        $table->string('serial_number')
        ->after('productCode')
        ->nullable();
        $table->tinyInteger("expiryChk")->default(0);
        $table->tinyInteger("packageChk")->default(0);
        $table->tinyInteger("x_y_prmoChk")->default(0);
        $table->tinyInteger("item_with_box")->default(0);
        
    });
      Schema::table('items', function($table){
            $table->foreign('category')->references('id')->on('categories')->onDelete('cascade');
        });
 }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function($table) {
        $table->dropForeign(['category']);
         });
    }
}
