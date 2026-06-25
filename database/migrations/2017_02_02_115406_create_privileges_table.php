<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrivilegesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('privileges', function (Blueprint $table) {
           
           $table->integer("roleId")->unsigned();
           $table->integer("resourceId")->unsigned();
           $table->integer("view");
           $table->integer("create");
           $table->integer("update");
           $table->integer("delete");
       });
         Schema::table('privileges', function($table){
            $table->foreign('roleId')->references('id')->on('roles')->onDelete('cascade');
        });

          Schema::table('privileges', function($table){
            $table->foreign('resourceId')->references('id')->on('resources')->onDelete('cascade');
        });
        

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('privileges', function($table) {
            $table->dropForeign(['roleId']);
              $table->dropForeign(['resourceId']);
        });
        Schema::dropIfExists('privileges');
    }
}
