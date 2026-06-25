<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateItemTableColumnMakeNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {

            $table->float('costPrise')
            ->nullable()
            ->default(0)
            ->change();

            $table->integer('min')
            ->nullable()
            ->default(0)
            ->change(); 

            $table->integer('max')
            ->nullable()
            ->default(0)
            ->change(); 

            $table->float("wholesale_salePrise")
            ->nullable()
            ->default(0)
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
        Schema::table('items', function (Blueprint $table) {

            $table->float('costPrise')->change();
            $table->integer('min')->nullable()->change(); 
            $table->integer('max')->nullable()->change(); 
            $table->float("wholesale_salePrise")->nullable()->change();

            
        });
    }
}
