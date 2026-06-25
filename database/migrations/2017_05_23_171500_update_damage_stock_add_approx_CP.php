<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDamageStockAddApproxCP extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('damage_stocks', function (Blueprint $table) {
            $table->double('approx_value_of_cp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('damage_stocks', function(Blueprint $table)
        {
            $table->dropColumn('approx_value_of_cp');
        });
    }
}
