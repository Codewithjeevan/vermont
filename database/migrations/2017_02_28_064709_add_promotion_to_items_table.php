<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPromotionToItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
     Schema::table('items', function($table) {
        $table->timestamp("promotion_from")->nullable()->after('image');
        $table->timestamp("promotion_to")->nullable()->after('promotion_from');
        $table->double('promotion_price')->nullable()->after('promotion_to');
    });
 }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function($table) {
            $table->dropColumn('promotion_from');
            $table->dropColumn('promotion_to');
            $table->dropColumn('promotion_price');
        });
    }
}
