<?php

namespace Database\Factories;

use App\Models\Guest;
use App\Models\Customer;
use App\Models\Room;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Reservation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $reservables = [
            Customer::class,
            Guest::class,
        ];
        $reservableType = $this->faker->randomElement($reservables);
        if ($reservableType == Guest::class) {
            $customer = Guest::factory(1)->create()[0];
        }
        else {
            $customer = Customer::all()->random();
        }
        $startDate = new Carbon($this->faker->dateTimeBetween("2020-01-01", "2021-12-31"));
        $days = $this->faker->numberBetween(0, 15);
        $endDate = $startDate->copy()->addDays($days);
        $today = Carbon::today();
        return [
            "reservable_type" => get_class($customer),
            "reservable_id" => $customer,
            "room_id" => Room::all()->random()->id,
            "start_date" => $startDate,
            "end_date" => $endDate,
            "check_in" => ($startDate < $today) ? $startDate : null,
            "check_out" => ($endDate < $today) ? $endDate : null,
        ];
    }
}
