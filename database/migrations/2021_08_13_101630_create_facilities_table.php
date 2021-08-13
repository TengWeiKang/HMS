<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Facility;

class CreateFacilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facility', function (Blueprint $table) {
            $table->id();
            $table->string("name")->unique();
        });

        Facility::create([
            "name" => "facility 1"
        ]);

        Facility::create([
            "name" => "facility 2"
        ]);

        Facility::create([
            "name" => "facility 3"
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('facility');
    }
}
