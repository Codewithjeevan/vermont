<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBillingTableAddLoyaltiDiccountPointsColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billings', function($table) {
            $table->double("loyalty_points")->after('subtotal');
            $table->double("loyalty_discount")->after('loyalty_points');
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
            $table->dropColumn("loyalty_points");
            $table->dropColumn("loyalty_discount");
        });
    }
}
