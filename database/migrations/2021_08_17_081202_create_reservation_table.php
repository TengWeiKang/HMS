<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservation', function (Blueprint $table) {
            $table->id();
            $table->integer("customer_id")->index();
            $table->integer("status")->default(1); // 0 - cancelled, 1 - available
            $table->date("start_date");
            $table->date("end_date");
            $table->double("deposit");
            $table->timestamp("check_in")->nullable();
            $table->timestamp("check_out")->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservation');
    }
}
