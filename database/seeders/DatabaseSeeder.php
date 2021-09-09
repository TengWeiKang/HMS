<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Customer;
use App\Models\Guest;
use App\Models\Employee;
use App\Models\Facility;
use App\Models\Room;
use App\Models\Service;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\RoomType;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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

        Guest::create([
            "username" => "guest1",
            "phone" => "012-12312311"
        ]);

        Guest::create([
            "username" => "guest2",
            "phone" => "012-12312312"
        ]);

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

        Facility::create([
            "name" => "Wi-Fi",
            "default" => 1
        ]);

        Facility::create([
            "name" => "Air Conditioning",
            "default" => 1
        ]);

        Facility::create([
            "name" => "Heater",
            "default" => 1
        ]);

        Facility::create([
            "name" => "Refrigerator",
            "default" => 0
        ]);

        Facility::create([
            "name" => "Car Park",
            "default" => 0
        ]);

        $roomType1 = RoomType::create([
            "name" => "Deluxe Room",
            "single_bed" => 2,
            "double_bed" => 1,
        ]);

        $roomType2 = RoomType::create([
            "name" => "Superior Room",
            "single_bed" => 1,
            "double_bed" => 2,
        ]);

        RoomType::create([
            "name" => "Demo Delete Room",
            "single_bed" => 1,
            "double_bed" => 1,
        ]);

        Room::create([
            "room_id" => "R101",
            "name" => "Room Name 1",
            "price" => 50.5,
            "room_type" => $roomType1->id,
            "room_image" => file_get_contents("public\\asset\\dashboard\\images\\room1.jpg"),
            "image_type" => "image/jpg",
            "single_bed" => 2,
            "double_bed" => 1,
        ])->facilities()->attach([2, 3, 4]);

        Room::create([
            "room_id" => "R102",
            "name" => "Room Name 2",
            "price" => 40,
            "room_type" => $roomType2->id,
            "room_image" => file_get_contents("public\\asset\\dashboard\\images\\room2.jpg"),
            "image_type" => "image/jpg",
            "single_bed" => 1,
            "double_bed" => 2,
        ])->facilities()->attach([1, 3]);

        Room::create([
            "room_id" => "R103",
            "name" => "Room Name 3",
            "price" => 70,
            "room_type" => $roomType2->id,
            "room_image" => file_get_contents("public\\asset\\dashboard\\images\\room3.jpg"),
            "image_type" => "image/jpg",
            "single_bed" => 2,
            "double_bed" => 2,
            "status" => 2
        ])->facilities()->attach([1, 2, 3, 4, 5]);

        Room::create([
            "room_id" => "R104",
            "name" => "Room Name 4",
            "price" => 22,
            "room_type" => $roomType1->id,
            "room_image" => file_get_contents("public\\asset\\dashboard\\images\\room4.jpg"),
            "image_type" => "image/jpg",
            "single_bed" => 2,
            "double_bed" => 2,
            "housekeptBy" => 3,
            "status" => 2
        ])->facilities()->attach([2]);

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

        Reservation::create([
            "room_id" => 1,
            "start_date" => Carbon::now()->today()->addDays(3),
            "end_date" => Carbon::now()->today()->addDays(5),
            "reservable_type" => Guest::class,
            "reservable_id" => 1,
        ]);

        Reservation::create([
            "room_id" => 2,
            "start_date" => Carbon::now()->today(),
            "end_date" => Carbon::now()->today()->addDays(3),
            "reservable_type" => Customer::class,
            "reservable_id" => 2,
        ]);

        Reservation::create([
            "room_id" => 2,
            "start_date" => Carbon::now()->today()->subDay(2),
            "end_date" => Carbon::now()->today()->subDay(),
            "reservable_type" => Guest::class,
            "reservable_id" => 2,
            "check_in" => Carbon::now()
        ])->services()->attach([
            ["service_id" => 2, "quantity" => 3],
            ["service_id" => 3, "quantity" => 2],
        ]);

        Reservation::create([
            "room_id" => 1,
            "start_date" => Carbon::now()->today()->subDays(5),
            "end_date" => Carbon::now()->today()->subDays(2),
            "reservable_type" => Customer::class,
            "reservable_id" => 1,
            "check_in" => Carbon::now()->subDays(7),
            "check_out" => Carbon::now()
        ])->services()->attach([
            ["service_id" => 1, "quantity" => 5],
            ["service_id" => 3, "quantity" => 10],
        ]);

        $reservation = Reservation::with("room")->find(4);
        $payment = Payment::create([
            "reservation_id" => $reservation->id,
            "room_id" => $reservation->room->id,
            "room_name" => $reservation->room->room_id . " - " . $reservation->room->name,
            "reservable_type" => $reservation->reservable_type,
            "reservable_id" => $reservation->reservable_id,
            "price_per_night" => $reservation->room->price,
            "start_date" => $reservation->start_date,
            "end_date" => $reservation->end_date,
            "payment_at" => Carbon::now()->subDays(5),
            "discount" => 20,
        ]);
        $payment->items()->createMany([
            ["service_name" => "food", "quantity" => 4, "unit_price" => 7],
            ["service_name" => "drink", "quantity" => 8, "unit_price" => 5],
        ]);
        $payment->charges()->createMany([
            ["description" => "late charge", "price" => 20.5],
            ["description" => "another charges", "price" => 40],
        ]);
    }
}
