<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSalesmanUserIdToBillingTable extends Migration
{
   /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
   {
    Schema::table('billings', function($table) {
      /*
      * default user superadmin
       */
      $table->integer("user_id")->after('id')->default(1);
      $table->integer("salesman_id")->nullable()->after('user_id');
    });
  }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
     Schema::table('billings', function($table) {
      $table->dropColumn('user_id');
      $table->dropColumn('salesman_id');
    });
   }
 }
