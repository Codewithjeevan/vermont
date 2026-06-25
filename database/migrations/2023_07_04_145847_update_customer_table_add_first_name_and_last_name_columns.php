<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCustomerTableAddFirstNameAndLastNameColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("customers", function(Blueprint $table){
            $table->string("firstName")->nullable();
            $table->string("lastName")->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("customers", function(Blueprint $table){
            $table->dropColumn('firstName');
            $table->dropColumn('lastName');
        });
    }
}
