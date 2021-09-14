<?php

namespace Database\Factories;

use App\Models\Guest;
use Illuminate\Database\Eloquent\Factories\Factory;

class GuestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Guest::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "username" => $this->faker->name(),
            "phone" => $this->faker->regexify("/^(\+6)?01[0-46-9]-[0-9]{7,8}$/"),
        ];
    }
}
