<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\RoomType;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    public function __construct()
    {
        $this->middleware("employee:admin");
    }

    public function index() {
        $roomTypes = RoomType::all();
        $payments = Payment::all();
        $years = $payments->groupBy(function ($value, $key) {
            return $value->payment_at->format("Y");
        })->keys()->sortDesc();
        return view("dashboard.analysis.index", ["roomTypes" => $roomTypes, "years" => $years]);
    }

    public function json(Request $request) {
        $json = [];
        $payments = Payment::with("items", "charges", "reservation", "reservation.room")->where("payment_at", "LIKE", $request->year . "%")->get();

        $json["revenueYearChart"] = $this->revenueYearChart($payments, $request->roomType);
        $json["revenueMonthChart"] = $this->revenueMonthChart($payments, $request->year, $request->month, $request->roomType);

        return $json;
    }

    private function revenueYearChart($payments, $roomType) {
        $json["bookings"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $json["services"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $json["charges"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

        $payments = $this->paymentFilterByRoomType($payments, $roomType);
        $bookingRevenues = $payments->groupBy(function ($value, $key) {
            return $value->payment_at->format("Y-m");
        });
        foreach ($bookingRevenues as $key => $value) {
            $month = (int) explode("-", $key)[1] - 1;
            $json["bookings"][$month] = $value->sum(function ($payment) {
                return $payment->bookingPriceWithDiscount();
            });
            $json["services"][$month] = $value->sum(function ($payment) {
                return $payment->totalItemPricesWithDiscount();
            });
            $json["charges"][$month] = $value->sum(function ($payment) {
                return $payment->totalChargesPrice();
            });
        }
        return $json;
    }

    private function revenueMonthChart($payments, $year, $month, $roomType) {
        $payments = $this->paymentFilterByRoomType($payments, $roomType);
        $info = $payments->groupBy(function ($value, $key) {
            return $value->payment_at->format("Y-m");
        })->get($year . "-" . $month, collect());
        $json = [
            $info->sum(function ($payment) {
                return $payment->bookingPriceWithDiscount();
            }),
            $info->sum(function ($payment) {
                return $payment->totalItemPricesWithDiscount();
            }),
            $info->sum(function ($payment) {
                return $payment->totalChargesPrice();
            })
        ];

        return $json;
    }

    private function paymentFilterByRoomType($payments, $roomType) {
        if (!empty($roomType)) {
            $payments = $payments->filter(function ($value) use ($roomType) {
                return $value->reservation->room->room_type == $roomType;
            });
        }
        return $payments;
    }
}
