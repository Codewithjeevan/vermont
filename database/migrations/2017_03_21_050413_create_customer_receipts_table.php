<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_receipts', function (Blueprint $table) {
            $table->increments('id');
            $table->date("receipt_date");
            $table->integer("customer_id");
            $table->integer("against_bill_id");
            $table->string("pay_mode");
            $table->string("cheque_draft_no")->nullable();
            $table->date("cheque_draft_date")->nullable();
            $table->integer("bank_id")->nullable();
            $table->string("dep_bank")->nullable();
            $table->double("receipt_amt");
            $table->string("remark")->nullable();
            
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
        Schema::dropIfExists('customer_receipts');
    }
}
