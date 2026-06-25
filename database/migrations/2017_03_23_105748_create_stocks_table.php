<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id')->unsigned();
            $table->integer('type')
            ->comment(" 1=open stock 
                        2=purchase 
                        3=purchase_return 
                        4=issue 
                        5=damage 
                        6=billing
                        7=package
                        8=offer
                        9=billing return
                        10=expiry
                        ");
            $table->integer('type_id');
            $table->double('stock');
            $table->date("date");            
        });
        Schema::table('stocks', function($table){
             $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('stocks', function($table) {
        $table->dropForeign(['item_id']);
       });
        Schema::dropIfExists('stocks');
    }
}
