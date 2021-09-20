<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_item', function (Blueprint $table) {
            $table->id();
            $table->integer("payment_id")->index();
            $table->integer("service_id")->index();
            $table->string("service_name");
            $table->integer("quantity");
            $table->double("unit_price");
            $table->timestamp("purchase_at");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_item');
    }
}
