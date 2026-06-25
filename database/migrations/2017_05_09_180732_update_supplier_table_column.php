<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSupplierTableColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suppliers', function (Blueprint $table) {


            $table->string('type')->nullable()->change();

            $table->integer('title')
            ->default(null)
            ->comment("1 = Mr. 2= Ms.  3= Mrs.")->nullable()->change();

            $table->string('contact')->nullable()->change(); 

            $table->longText('address')->nullable()->change(); 

            $table->string('phone')->nullable()->change(); 

            $table->string('mobile')->nullable()->change(); 

            $table->string('fax')->nullable()->change(); 

            $table->string('email')->nullable()->change(); 

            $table->string('city')->nullable()->change(); 

            $table->string('ecc')->nullable()->change(); 

            $table->string('cst')->nullable()->change(); 

            $table->string('range')->nullable()->change(); 

            $table->string('division')->nullable()->change(); 

            $table->string('comm')->nullable()->change(); 

            $table->string('pan')->nullable()->change(); 

            $table->string('tin')->nullable()->change();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {


            $table->string('type')->change();

            $table->integer('title')
            ->default("1")
            ->comment("1 = Mr. 2= Ms.  3= Mrs.")->change();

            $table->string('contact')->change(); 

            $table->longText('address')->change(); 

            $table->string('phone')->change(); 

            $table->string('mobile')->change(); 

            $table->string('fax')->change(); 

            $table->string('email')->change(); 

            $table->string('city')->change(); 

            $table->string('ecc')->change(); 

            $table->string('cst')->change(); 

            $table->string('range')->change(); 

            $table->string('division')->change(); 

            $table->string('comm')->change(); 

            $table->string('pan')->change(); 

            $table->string('tin')->change(); 

            

            
        });
    }
}
