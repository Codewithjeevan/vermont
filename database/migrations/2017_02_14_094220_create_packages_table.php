<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->integer('itemId')->unsigned();
            $table->integer('packageId')->unsigned();
            $table->double('qty');
        });
           Schema::table('packages', function($table){
            $table->foreign('itemId')->references('id')->on('items')->onDelete('cascade')->onDelete('cascade');
        });
          Schema::table('packages', function($table){
            $table->foreign('packageId')->references('id')->on('items')->onDelete('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('packages', function($table) {
        $table->dropForeign(['itemId']);
        $table->dropForeign(['packageId']);
        
       });
        Schema::dropIfExists('packages');
    }
}
