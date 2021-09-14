<?php

namespace Database\Factories;

use App\Models\PaymentCharge;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentChargeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PaymentCharge::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "description" => $this->faker->word(3),
            "price" => $this->faker->randomFloat(2, 1, 50),
        ];
    }
}
