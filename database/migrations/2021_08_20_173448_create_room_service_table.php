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

        Reservation::create([
            "room_id" => 1,
            "start_date" => Carbon::now()->today(),
            "end_date" => Carbon::now()->today()->addDays(5),
            "reservable_type" => Customer::class,
            "reservable_id" => 1,
            "check_in" => Carbon::now()
        ])->services()->attach([
            ["service_id" => 1, "quantity" => 5],
            ["service_id" => 3, "quantity" => 10],
        ]);
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
