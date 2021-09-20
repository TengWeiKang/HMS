<?php

namespace Database\Factories;

use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RoomType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "name" => $this->faker->unique()->word,
            "single_bed" => $this->faker->randomDigit(),
            "double_bed" => $this->faker->randomDigit(),
            'price' => $this->faker->randomFloat(0, 20, 100),
            'room_image' => file_get_contents($this->faker->image(null, 300, 300)),
            'image_type' => "image/jpg",
        ];
    }
}
