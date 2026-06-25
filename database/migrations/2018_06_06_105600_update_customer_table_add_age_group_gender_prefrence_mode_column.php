<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCustomerTableAddAgeGroupGenderPrefrenceModeColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("customers", function(Blueprint $table){
            $table->integer("gender")->after('points')->default(0);
            $table->integer("age_group")->after('gender')->default(0);
            $table->integer("prefered_mode")->after('age_group')->default(0);
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
        $table->dropColumn('gender');
        $table->dropColumn('age_group');
        $table->dropColumn('prefered_mode');
        });
    }
}
