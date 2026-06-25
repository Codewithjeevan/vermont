<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBillReturnItemsTableAddIdAndExistId extends Migration
{
   /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
   {
    Schema::table('bill_return_items', function (Blueprint $table) {
        $table->increments('id')->first();
        $table->double('existId')->nullable()->default(0);

    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_return_items', function(Blueprint $table)
        {
            $table->dropColumn('id');
            $table->dropColumn('existId');
        });
    }
}
