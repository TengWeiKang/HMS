<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Service;

class CreateServiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->double("price");
            $table->timestamps();
        });

        Service::create([
            "name" => "food",
            "price" => 7
        ]);
        Service::create([
            "name" => "drink",
            "price" => 5
        ]);
        Service::create([
            "name" => "food 2",
            "price" => 9
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service');
    }
}
