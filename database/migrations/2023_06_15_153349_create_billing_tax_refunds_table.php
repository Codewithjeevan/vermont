<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingTaxRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_tax_refunds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('billId'); 
            $table->string('receiptNumber'); 
            $table->double('totalAmount');
            $table->double('vatAmount');
            $table->double('taxRefundAmount');
            $table->string('taxRegisterStatus');
            $table->string('taxRegisterTagNumber');
            $table->string('taxRefundTagNumber');
            $table->string('taxRefundStatus');
            $table->string('taxRefundQrCode');
            $table->double('refundAmount');
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('billing_tax_refunds');
    }
}
