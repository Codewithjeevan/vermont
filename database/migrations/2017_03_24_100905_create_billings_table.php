<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billings', function (Blueprint $table) {
            $table->increments('id');

            $table->string("billing_status");

            $table->integer("invoice_type")->comment('1-retail 2-wholesale')->default(1);

            $table->integer("payment_mode")->comment('1-cash 2-card & cash 3-credit 4-credit card')->default(1);

            $table->string("card_no")->nullable();

            $table->integer("customer_id")->nullable();

            $table->double("subtotal");

            $table->double("dis_amt");

            $table->double("v_amt");

            $table->double("cash_amt");

            $table->double("card_amt");

            $table->double("bil_amt");

            $table->double("received");

            $table->double("balance");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('billings');
    }
}
