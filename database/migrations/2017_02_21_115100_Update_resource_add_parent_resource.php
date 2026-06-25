<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateResourceAddParentResource extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("resources", function(Blueprint $table){
            $table->integer("parent_resource")
                    ->default(0)->unsigned();
        });
        // Schema::table("resources", function(Blueprint $table){
        //     $table->integer("parent_resource")
        //             ->default(0);
        //     $table->foreign("parent_resource")
        //         ->references("resources")
        //         ->on("id");
        // });
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
