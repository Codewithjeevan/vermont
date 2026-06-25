<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeInDamageStock extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('damage_stocks', function (Blueprint $table) {
            $table->integer('type')
            ->after('id')
            ->comment('0 - Damage , 1 - Expiry')
            ->default(0);

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
            $table->dropColumn('type');
        });
    }
}
