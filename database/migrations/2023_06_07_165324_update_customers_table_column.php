<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCustomersTableColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("customers", function(Blueprint $table){
            $table->string("nationality")->nullable();
            $table->string("countryOfResidence")->nullable();
            $table->string("type")->nullable();
            $table->string("issuedBy")->nullable();
            $table->string("number")->nullable();

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
            $table->dropColumn('nationality');
            $table->dropColumn('countryOfResidence');
            $table->dropColumn('type');
            $table->dropColumn('issuedBy');
            $table->dropColumn('number');

            });
    }
}
