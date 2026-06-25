<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCategoryTableAddParent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table("categories", function(Blueprint $table){
        //     $table->integer("parent_category")->default(0);
        //     $table->foreign('parent_category')
        //     ->references('id')
        //     ->on('categories');
        // });
         Schema::table("categories", function(Blueprint $table){
            $table->integer("parent_category")->default(0)->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
