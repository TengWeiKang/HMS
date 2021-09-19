<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRoomTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_type', function (Blueprint $table) {
            $table->id();
            $table->string("name")->unique();
            $table->integer("single_bed");
            $table->integer("double_bed");
            $table->string("image_type");
            $table->double("price");
        });
        DB::statement("ALTER TABLE room_type ADD room_image LONGBLOB NOT NULL AFTER double_bed");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_type');
    }
}
