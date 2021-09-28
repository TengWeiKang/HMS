<?php

namespace Database\Factories;

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
    private $collections = null;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        if (is_null($this->collections))
            $this->collections = collect();
        $customer = Customer::all()->random();
        $rooms = Room::all();
        $startDate = $endDate = $roomID = null;
        do {
            $startDate = new Carbon($this->faker->dateTimeBetween("2020-01-01", "2021-12-31"));
            $startDate->setHour(0);
            $startDate->setMinute(0);
            $startDate->setSecond(0);
            $days = $this->faker->numberBetween(0, 15);
            $endDate = $startDate->copy()->addDays($days);
            $roomID = $rooms->random()->id;
            $collections = $this->collections->filter(function ($reservation) use ($roomID, $startDate, $endDate) {
                return $reservation["room_id"] == $roomID &&
                ($reservation["start_date"] <= $startDate && $reservation["end_date"] >= $startDate ||
                $reservation["start_date"] <= $endDate && $reservation["end_date"] >= $endDate ||
                $reservation["start_date"] >= $startDate && $reservation["end_date"] <= $endDate);
            });
            $count = $collections->count();
        } while ($count > 0);
        $today = Carbon::today();
        $this->collections->push(["room_id" => $roomID, "start_date" => $startDate, "end_date" => $endDate]);
        return [
            "customer_id" => $customer,
            "room_id" => $roomID,
            "start_date" => $startDate,
            "end_date" => $endDate,
            "check_in" => ($startDate < $today) ? $startDate : null,
            "check_out" => ($endDate < $today) ? $endDate : null,
            "created_at" => $startDate,
        ];
    }
}
