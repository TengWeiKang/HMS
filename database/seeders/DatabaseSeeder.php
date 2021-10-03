<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Customer;
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
            "passport" => "px123456",
            "first_name" => "john",
            "last_name" => "tan",
            "phone" => "012-3456789",
            "password" => Hash::make("123456789")
        ]);

        Customer::create([
            "username" => "customer2",
            "email" => "customer2@gmail.com",
            "phone" => "012-98765432",
            "passport" => "px123457",
            "first_name" => "jane",
            "last_name" => "lee",
            "phone" => "012-98765432",
            "password" => Hash::make("123456789")
        ]);

        Employee::create([
            "username" => "Ali",
            "email" => "admin@gmail.com",
            "phone" => "012-3456789",
            "password" => Hash::make("123456789"),
            "role" => 0
        ]);

        Employee::create([
            "username" => "bobby",
            "email" => "frontdesk@gmail.com",
            "phone" => "012-3456789",
            "password" => Hash::make("123456789"),
            "role" => 1
        ]);

        Employee::create([
            "username" => "lili",
            "email" => "weikangteng@gmail.com",
            "phone" => "012-3456789",
            "password" => Hash::make("123456789"),
            "role" => 2
        ]);

        Facility::create([
            "name" => "Wi-Fi",
        ]);

        Facility::create([
            "name" => "Air Conditioning",
        ]);

        Facility::create([
            "name" => "Heater",
        ]);

        Facility::create([
            "name" => "Refrigerator",
        ]);

        Facility::create([
            "name" => "Car Park",
        ]);

        $roomType1 = RoomType::create([
            "name" => "Deluxe Room",
            "single_bed" => 2,
            "double_bed" => 1,
            "price" => 300,
            "room_image" => file_get_contents("public\\asset\\dashboard\\images\\room1.jpg"),
            "image_type" => "image/jpg",
        ]);
        $roomType1->facilities()->attach([1,2,5]);

        $roomType2 = RoomType::create([
            "name" => "Superior Room",
            "single_bed" => 1,
            "double_bed" => 2,
            "price" => 250,
            "room_image" => file_get_contents("public\\asset\\dashboard\\images\\room2.jpg"),
            "image_type" => "image/jpg",
        ]);
        $roomType2->facilities()->attach([2,4,5]);

        RoomType::create([
            "name" => "Demo Delete Room",
            "single_bed" => 1,
            "double_bed" => 1,
            "price" => 70,
            "room_image" => file_get_contents("public\\asset\\dashboard\\images\\room3.jpg"),
            "image_type" => "image/jpg",
        ]);

        Room::create([
            "room_id" => "R101",
            "name" => "Room Name 1",
            "room_type" => $roomType1->id,
            "single_bed" => 2,
            "double_bed" => 1,
        ]);

        Room::create([
            "room_id" => "R102",
            "name" => "Room Name 2",
            "room_type" => $roomType2->id,
            "single_bed" => 1,
            "double_bed" => 2,
        ]);

        Room::create([
            "room_id" => "R103",
            "name" => "Room Name 3",
            "room_type" => $roomType2->id,
            "single_bed" => 2,
            "double_bed" => 2,
            "status" => 2
        ]);

        Room::create([
            "room_id" => "R104",
            "name" => "Room Name 4",
            "room_type" => $roomType1->id,
            "single_bed" => 1,
            "double_bed" => 1,
            "housekeep_by" => 3,
            "status" => 2
        ]);

        Room::create([
            "room_id" => "R105",
            "name" => "Room Name 5",
            "room_type" => $roomType1->id,
            "single_bed" => 0,
            "double_bed" => 2,
            "status" => 2
        ]);

        $service1 = Service::create([
            "name" => "food",
            "price" => 7
        ]);

        $service2 = Service::create([
            "name" => "drink",
            "price" => 5
        ]);

        $service3 = Service::create([
            "name" => "food 2",
            "price" => 9
        ]);

        // Reservation::create([
        //     "room_id" => 1,
        //     "start_date" => Carbon::now()->today()->addDays(3),
        //     "end_date" => Carbon::now()->today()->addDays(5),
        //     "customer_id" => 1,
        // ]);

        // Reservation::create([
        //     "room_id" => 2,
        //     "start_date" => Carbon::now()->today(),
        //     "end_date" => Carbon::now()->today()->addDays(3),
        //     "customer_id" => 2,
        // ]);

        // Reservation::create([
        //     "room_id" => 2,
        //     "start_date" => Carbon::now()->today(),
        //     "end_date" => Carbon::now()->today()->addDays(3),
        //     "customer_id" => 1,
        //     "status" => 0
        // ]);

        // Reservation::create([
        //     "room_id" => 4,
        //     "start_date" => Carbon::now()->today()->subDay(2),
        //     "end_date" => Carbon::now()->today()->subDay(),
        //     "customer_id" => 2,
        //     "check_in" => Carbon::now()
        // ]);

        // Reservation::create([
        //     "room_id" => 2,
        //     "start_date" => Carbon::now()->today()->subDay(2),
        //     "end_date" => Carbon::now()->today()->subDay(),
        //     "customer_id" => 2,
        //     "check_in" => Carbon::now()
        // ])->services()->attach([
        //     ["service_id" => 2, "quantity" => 3],
        //     ["service_id" => 3, "quantity" => 2],
        // ]);

        // $reservation = Reservation::create([
        //     "room_id" => 1,
        //     "start_date" => Carbon::now()->today()->subDays(5),
        //     "end_date" => Carbon::now()->today()->subDays(2),
        //     "customer_id" => 1,
        //     "check_in" => Carbon::now()->subDays(7),
        //     "check_out" => Carbon::now()
        // ]);
        // $reservation->services()->attach([
        //     ["service_id" => 1, "quantity" => 5, "created_at" => Carbon::now()->subDay()],
        //     ["service_id" => 3, "quantity" => 10, "created_at" => Carbon::now()->subDay()],
        //     ["service_id" => 1, "quantity" => 5],
        //     ["service_id" => 2, "quantity" => 7],
        // ]);

        // $payment = Payment::create([
        //     "reservation_id" => $reservation->id,
        //     "room_name" => $reservation->room->room_id . " - " . $reservation->room->name,
        //     "price_per_night" => $reservation->room->type->price,
        //     "start_date" => $reservation->start_date,
        //     "end_date" => $reservation->end_date,
        //     "payment_at" => Carbon::now()->subDays(5),
        //     "discount" => 20,
        // ]);
        // $payment->items()->createMany([
        //     ["service_id" => $service1->id, "service_name" => $service1->name, "quantity" => 5, "unit_price" => $service1->price, "purchase_at" => Carbon::now()->subDay()],
        //     ["service_id" => $service3->id, "service_name" => $service3->name, "quantity" => 10, "unit_price" => $service3->price, "purchase_at" => Carbon::now()->subDay()],
        //     ["service_id" => $service1->id, "service_name" => $service1->name, "quantity" => 5, "unit_price" => $service1->price, "purchase_at" => Carbon::now()],
        //     ["service_id" => $service2->id, "service_name" => $service2->name, "quantity" => 7, "unit_price" => $service2->price, "purchase_at" => Carbon::now()],
        // ]);
        // $payment->charges()->createMany([
        //     ["description" => "late charge", "price" => 20.5],
        //     ["description" => "another charges", "price" => 40],
        // ]);

        // $reservation2 = Reservation::create([
        //     "room_id" => 2,
        //     "start_date" => Carbon::now()->today()->subDay(34),
        //     "end_date" => Carbon::now()->today()->subDay(31),
        //     "customer_id" => 1,
        //     "check_in" => Carbon::now()->subDay(34),
        //     "check_out" => Carbon::now()->subDay(31)
        // ]);

        // $reservation2->services()->attach([
        //     ["service_id" => 2, "quantity" => 3, "created_at" => Carbon::now()->subDay(34)],
        //     ["service_id" => 3, "quantity" => 2, "created_at" => Carbon::now()->subDay(33)],
        // ]);

        // $payment2 = Payment::create([
        //     "reservation_id" => $reservation2->id,
        //     "room_name" => $reservation2->room->room_id . " - " . $reservation2->room->name,
        //     "price_per_night" => $reservation2->room->type->price,
        //     "start_date" => $reservation2->start_date,
        //     "end_date" => $reservation2->end_date,
        //     "payment_at" => Carbon::now()->subDays(31),
        //     "discount" => 20,
        // ]);
        // $payment2->items()->createMany([
        //     ["service_id" => $service2->id, "service_name" => $service2->name, "quantity" => 3, "unit_price" => $service2->price, "purchase_at" => Carbon::now()->subDay(34)],
        //     ["service_id" => $service3->id, "service_name" => $service3->name, "quantity" => 5, "unit_price" => $service3->price, "purchase_at" => Carbon::now()->subDay(33)],
        // ]);
        // $payment2->charges()->createMany([
        //     ["description" => "late charge", "price" => 20.5],
        //     ["description" => "another charges", "price" => 40],
        // ]);
    }
}
