<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment', function (Blueprint $table) {
            $table->id();
            $table->string("reservation_id")->index();
            $table->string("room_id")->index();
            $table->string("room_name");
            $table->string("reservable_type");
            $table->string("reservable_id");
            $table->double("price_per_night");
            $table->string("start_date");
            $table->string("end_date");
            $table->double("discount");
            $table->timestamp("payment_at");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment');
    }
}
