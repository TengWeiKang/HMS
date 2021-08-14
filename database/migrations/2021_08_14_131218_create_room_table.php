<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->double("price");
            $table->integer("single_bed");
            $table->integer("double_bed");
            $table->string("image_type");
            $table->timestamp('created_at')->nullable();
        });

        DB::statement("ALTER TABLE room ADD room_image LONGBLOB NOT NULL AFTER double_bed");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room');
    }
}
