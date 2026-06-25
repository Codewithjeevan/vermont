<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->increments('id');

            $table->string('type');

            $table->integer('title')
            ->default("1")
            ->comment("1 = Mr. 2= Ms.  3= Mrs.");

            $table->string('name');

            $table->string('contact'); 

            $table->longText('address'); 

            $table->string('phone'); 

            $table->string('mobile'); 

            $table->string('fax'); 

            $table->string('email'); 

            $table->string('city'); 

            $table->string('ecc'); 

            $table->string('cst'); 

            $table->string('range'); 

            $table->string('division'); 

            $table->string('comm'); 

            $table->string('pan'); 

            $table->string('tin'); 

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
        Schema::dropIfExists('suppliers');
    }
}
