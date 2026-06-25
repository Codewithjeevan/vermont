<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBillingAddOfferwithColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billing_items', function($table) {
            $table->integer("offerwith")->default(0);
            $table->string("serial_check")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('billing_items', function($table) {
            $table->dropColumn('offerwith');
            $table->dropColumn('serial_check');
        });
    }
}
