<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('purchases', function (Blueprint $table) {
       $table->increments('id');
       $table->date("mrn_date");
       $table->integer('po_no')->nullable();
       $table->integer('supplier_id');
       $table->date("supplier_billdate");
       $table->string('supplierBillNo');
       $table->string('purchase');
       $table->string('receivedBy');      
       $table->double('subTotal');
       $table->double('disAmount');
       $table->double('extraDiscount');
       $table->double('billAmount');

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
      Schema::dropIfExists('purchases');
    }
  }
