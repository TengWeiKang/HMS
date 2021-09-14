<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Facility;
use App\Models\PaymentCharge;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Service;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RandomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();
        Employee::create([
            "username" => "admin",
            "email" => "admin@gmail.com",
            "phone" => "012-3456789",
            "password" => Hash::make("123456789"),
            "role" => 0
        ]);

        Employee::create([
            "username" => "frontdesk",
            "email" => "frontdesk@gmail.com",
            "phone" => "012-3456789",
            "password" => Hash::make("123456789"),
            "role" => 1
        ]);

        Employee::create([
            "username" => "housekeeper",
            "email" => "housekeeper@gmail.com",
            "phone" => "012-3456789",
            "password" => Hash::make("123456789"),
            "role" => 2
        ]);

        Customer::create([
            "username" => "customer1",
            "email" => "customer1@gmail.com",
            "phone" => "012-3456789",
            "password" => Hash::make("123456789")
        ]);

        Customer::create([
            "username" => "customer2",
            "email" => "customer2@gmail.com",
            "phone" => "012-3456789",
            "password" => Hash::make("123456789")
        ]);

        $facilities = Facility::factory()->count(10)->create();

        Customer::factory()->count(5)->create();

        Service::factory()->count(5)->create();

        RoomType::factory()->times($faker->numberBetween(6, 8))->create()->each(function ($roomType) use ($faker, $facilities) {
            $roomType->rooms()->saveMany(Room::factory()->times($faker->numberBetween(0, 5))->make());
            $roomType->rooms->each(function ($room) use ($faker, $facilities) {
                $selectedFacilities = $facilities->random($faker->numberBetween(0, $facilities->count()))->pluck("id");
                $room->facilities()->sync($selectedFacilities);
            });
        });
        $services = Service::all();
        Reservation::factory()->count(400)->create()->each(function($reservation) use ($faker, $services) {
            $selectedServices = $services->random($faker->numberBetween(0, $services->count()));
            $items = [];
            foreach ($selectedServices as $service) {
                $random = $faker->numberBetween(1, 10);
                $reservation->services()->attach($service, [
                    "quantity" => $random
                ]);
                $items[] = [
                    "service_name" => $service->name,
                    "quantity" => $random,
                    "unit_price" => $service->price
                ];
            }
            if ($reservation->check_out == null)
                return;
            $payment = $reservation->payment()->create([
                "reservation_id" => $reservation->id,
                "room_id" => $reservation->room_id,
                "room_name" => $reservation->room->name,
                "price_per_night" => $reservation->room->price,
                "start_date" => $reservation->start_date,
                "end_date" => $reservation->end_date,
                "discount" => $faker->numberBetween(0, 20),
                "payment_at" => $reservation->check_out,
            ]);

            $randomCharge = $faker->numberBetween(0, 5);
            $payment->items()->createMany($items);
            $payment->charges()->saveMany(PaymentCharge::factory()->times($randomCharge)->make());
        });
    }
}
