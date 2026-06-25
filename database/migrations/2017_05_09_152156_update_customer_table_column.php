<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCustomerTableColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {

            $table->string("title")
            ->nullable()
            ->default(null)
            ->comment("1 = Mr. 2= Ms.  3= Mrs.")
            ->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function(Blueprint $table)
        {
            $table->string("title")
            ->default(1)
            ->comment("1 = Mr. 2= Ms.  3= Mrs.")
            ->change();
        });
    }
}
