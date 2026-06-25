<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanetAuthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('planet_auths', function (Blueprint $table) {
            $table->increments('id');
            $table->string('client_id'); 
            $table->string('client_secret'); 
            $table->string('terminal_code');
            $table->string('vat_code');
            $table->string('merchandise_group_code');
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('planet_auths');
    }
}
