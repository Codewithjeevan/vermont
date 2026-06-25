<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePlanetAuthTableAddPaymentCodeAndShopNameColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("planet_auths", function(Blueprint $table){
            $table->string("store_name")->after('id');
            $table->string("payment_method_code");
            $table->string("user_name");
            $table->string("password");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("planet_auths", function(Blueprint $table){
            $table->dropColumn('store_name');
            $table->dropColumn('payment_method_code');
            $table->dropColumn('user_name');
            $table->dropColumn("password");
        });
    }
}
