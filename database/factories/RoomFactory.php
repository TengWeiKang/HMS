<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Room::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "room_id" => $this->faker->unique->regexify("/^R\d{3}$/"),
            "name" => $this->faker->word(2),
            "single_bed" => $this->faker->randomDigit(),
            "double_bed" => $this->faker->randomDigit(),
        ];
    }
}
