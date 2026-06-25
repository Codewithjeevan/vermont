<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCustomersTableAddNewColumnsEligibityPoints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table("customers", function(Blueprint $table){
            $table->integer("eligibility")->after('city')->default(0);
            $table->double("points")->after('eligibility')->default(0);
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
        $table->dropColumn('eligibility');
        $table->dropColumn('points');
        });
    }
}
