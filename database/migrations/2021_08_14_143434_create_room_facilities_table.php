<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Room;

class CreateRoomFacilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_facility', function (Blueprint $table) {
            $table->id();
            $table->integer("room_id")->index();
            $table->integer("facility_id")->index();
        });

        $room = Room::create([
            "room_id" => "R101",
            "name" => "Room Name 1",
            "price" => 50.5,
            "room_image" => file_get_contents(asset("dashboard\\images\\hotel_placeholder.png")),
            "image_type" => "image/png",
            "single_bed" => 2,
            "double_bed" => 1,
        ])->facilities()->attach([2, 3]);

        $room = Room::create([
            "room_id" => "R102",
            "name" => "Room Name 2",
            "price" => 40,
            "room_image" => file_get_contents(asset("dashboard\\images\\hotel_placeholder.png")),
            "image_type" => "image/png",
            "single_bed" => 1,
            "double_bed" => 2,
        ])->facilities()->attach([1, 3]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_facility');
    }
}
