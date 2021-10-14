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
            "phone" => "014-6000816",
            "password" => Hash::make("123456789"),
            "role" => 2
        ]);

        Employee::create([
            "username" => "Marry",
            "email" => "testing@gmail.com",
            "phone" => "014-6000816",
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
            "name" => "Deluxe Room 1",
            "room_type" => $roomType1->id,
            "single_bed" => 2,
            "double_bed" => 1,
        ]);

        Room::create([
            "room_id" => "R102",
            "name" => "Deluxe Room 2",
            "room_type" => $roomType1->id,
            "single_bed" => 1,
            "double_bed" => 2,
        ]);

        Room::create([
            "room_id" => "R103",
            "name" => "Superior Room 1",
            "room_type" => $roomType2->id,
            "single_bed" => 2,
            "double_bed" => 2,
        ]);

        Room::create([
            "room_id" => "R104",
            "name" => "Superior Room 2",
            "room_type" => $roomType2->id,
            "single_bed" => 1,
            "double_bed" => 1,
        ]);

        Room::create([
            "room_id" => "R105",
            "name" => "Deluxe Room 3",
            "room_type" => $roomType1->id,
            "single_bed" => 1,
            "double_bed" => 1,
        ]);

        Room::create([
            "room_id" => "R106",
            "name" => "Deluxe Room 4",
            "room_type" => $roomType1->id,
            "single_bed" => 1,
            "double_bed" => 2,
            "status" => 0
        ]);

        Room::create([
            "room_id" => "R107",
            "name" => "Deluxe Room 5",
            "room_type" => $roomType1->id,
            "single_bed" => 1,
            "double_bed" => 2,
            "status" => 0
        ]);

        Room::create([
            "room_id" => "R108",
            "name" => "Superior Room 3",
            "room_type" => $roomType2->id,
            "single_bed" => 1,
            "double_bed" => 1,
            "status" => 2,
            "housekeep_by" => 3
        ]);

        $service1 = Service::create([
            "name" => "SPA",
            "price" => 7
        ]);

        $service2 = Service::create([
            "name" => "Meal",
            "price" => 5
        ]);

        $service3 = Service::create([
            "name" => "Mineral Water",
            "price" => 9
        ]);

        Reservation::create([
            "start_date" => Carbon::now()->addDays(3),
            "end_date" => Carbon::now()->addDays(5),
            "customer_id" => 1,
            "deposit" => 300,
        ])->rooms()->attach([1,3,5]);

        Reservation::create([
            "start_date" => Carbon::now(),
            "end_date" => Carbon::now()->addDays(3),
            "customer_id" => 2,
            "deposit" => 200,
        ])->rooms()->attach([2,6]);

        Reservation::create([
            "start_date" => Carbon::now(),
            "end_date" => Carbon::now()->addDays(3),
            "customer_id" => 1,
            "status" => 0,
            "deposit" => 400,
        ])->rooms()->attach([1,2,6,7]);

        $checkInReservation = Reservation::create([
            "start_date" => Carbon::now()->subDay(2),
            "end_date" => Carbon::now()->subDay(),
            "customer_id" => 1,
            "check_in" => Carbon::now(),
            "deposit" => 300,
        ]);
        $checkInReservation->rooms()->attach([1,3,5]);
        $checkInReservation->services()->attach([
            ["service_id" => $service1->id, "quantity" => 5, "created_at" => Carbon::now()->subDay(2)],
            ["service_id" => $service2->id, "quantity" => 4, "created_at" => Carbon::now()->subDay()],
        ]);

        $reservation = Reservation::create([
            "start_date" => Carbon::now()->subDay(2),
            "end_date" => Carbon::now()->subDay(),
            "customer_id" => 2,
            "check_in" => Carbon::now()->subDay(2),
            "check_out" => Carbon::now(),
            "deposit" => 100,
        ]);
        $reservation->rooms()->attach([4]);
        $reservation->services()->attach([
            ["service_id" => $service2->id, "quantity" => 3, "created_at" => Carbon::now()->subDay(2)],
            ["service_id" => $service3->id, "quantity" => 2, "created_at" => Carbon::now()->subDay(2)],
        ]);

        $payment = Payment::create([
            "reservation_id" => $reservation->id,
            "start_date" => $reservation->start_date,
            "end_date" => $reservation->end_date,
            "payment_at" => $reservation->end_date->addDays(),
            "discount" => 20,
            "deposit" => $reservation->deposit,
        ]);
        $payment->rooms()->attach($reservation->rooms->mapWithKeys(function ($room) {
            return [$room->id => ["price_per_night" => $room->type->price]];
        }));
        $payment->items()->createMany($reservation->services->map(function ($service) {
            return ["service_id" => $service->id, "service_name" => $service->name, "quantity" => $service->pivot->quantity, "unit_price" => $service->price, "purchase_at" => $service->created_at];
        }));
        $payment->charges()->createMany([
            ["description" => "late charge", "price" => 20.5],
            ["description" => "another charges", "price" => 40],
        ]);

        $reservation2 = Reservation::create([
            "start_date" => Carbon::now()->subDays(5),
            "end_date" => Carbon::now()->subDays(2),
            "customer_id" => 1,
            "check_in" => Carbon::now()->subDays(5),
            "check_out" => Carbon::now()->subDays(1),
            "deposit" => 300,
        ]);
        $reservation2->rooms()->attach([2,6,7]);
        $reservation2->services()->attach([
            ["service_id" => $service1->id, "quantity" => 5, "created_at" => Carbon::now()->subDay(4)],
            ["service_id" => $service3->id, "quantity" => 10, "created_at" => Carbon::now()->subDay(4)],
            ["service_id" => $service1->id, "quantity" => 5, "created_at" => Carbon::now()->subDay(2)],
            ["service_id" => $service2->id, "quantity" => 7, "created_at" => Carbon::now()->subDay(2)],
        ]);

        $payment2 = Payment::create([
            "reservation_id" => $reservation2->id,
            "start_date" => $reservation2->start_date,
            "end_date" => $reservation2->end_date,
            "payment_at" => $reservation2->end_date->addDays(),
            "discount" => 20,
            "deposit" => $reservation2->deposit,
        ]);
        $payment2->rooms()->attach($reservation2->rooms->mapWithKeys(function ($room) {
            return [$room->id => ["price_per_night" => $room->type->price]];
        }));
        $payment2->items()->createMany($reservation2->services->map(function ($service) {
            return ["service_id" => $service->id, "service_name" => $service->name, "quantity" => $service->pivot->quantity, "unit_price" => $service->price, "purchase_at" => $service->created_at];
        }));
        $payment2->charges()->createMany([
            ["description" => "late charge", "price" => 20.5],
            ["description" => "another charges", "price" => 30],
        ]);

        $reservation3 = Reservation::create([
            "start_date" => Carbon::now()->today()->subDay(34),
            "end_date" => Carbon::now()->today()->subDay(31),
            "customer_id" => 1,
            "check_in" => Carbon::now()->subDay(34),
            "check_out" => Carbon::now()->subDay(30),
            "deposit" => 400
        ]);
        $reservation3->rooms()->attach([1,3,4,5]);
        $reservation3->services()->attach([
            ["service_id" => $service2->id, "quantity" => 3, "created_at" => Carbon::now()->subDay(34)],
            ["service_id" => $service3->id, "quantity" => 2, "created_at" => Carbon::now()->subDay(33)],
        ]);

        $payment3 = Payment::create([
            "reservation_id" => $reservation3->id,
            "start_date" => $reservation3->start_date,
            "end_date" => $reservation3->end_date,
            "payment_at" => $reservation3->end_date->addDays(),
            "discount" => 20,
            "deposit" => $reservation3->deposit,
        ]);
        $payment3->rooms()->attach($reservation3->rooms->mapWithKeys(function ($room) {
            return [$room->id => ["price_per_night" => $room->type->price]];
        }));
        $payment3->items()->createMany($reservation3->services->map(function ($service) {
            return ["service_id" => $service->id, "service_name" => $service->name, "quantity" => $service->pivot->quantity, "unit_price" => $service->price, "purchase_at" => $service->created_at];
        }));
        $payment3->charges()->createMany([
            ["description" => "late charge", "price" => 20.5],
            ["description" => "another charges", "price" => 30],
        ]);
    }
}
