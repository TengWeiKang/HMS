<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Reservation;
use App\Models\Customer;
use Carbon\Carbon;

class CreateRoomServiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_service', function (Blueprint $table) {
            $table->id();
            $table->integer("reservation_id")->index();
            $table->integer("service_id")->index();
            $table->integer("quantity");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_service');
    }
}
