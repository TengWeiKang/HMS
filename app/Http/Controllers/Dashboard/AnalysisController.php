<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    public function __construct()
    {
        $this->middleware("employee:admin");
    }

    public function index() {
        $payments = Payment::all();
        $years = $payments->groupBy(function ($value, $key) {
            return $value->payment_at->format("Y");
        })->keys()->sortDesc();
        return view("dashboard.analysis.index", ["years" => $years]);
    }

    public function json(Request $request) {
        $json = [];
        $payments = Payment::with("items", "charges")->where("payment_at", "LIKE", $request->year . "%")->get();

        $json["revenueYearChart"] = $this->revenueYearChart($payments);
        $json["revenueMonthChart"] = $this->revenueMonthChart($payments, $request->year, $request->month);

        return $json;
    }

    private function revenueYearChart($payments) {
        $json["bookings"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $json["services"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $json["charges"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

        $bookingRevenues = $payments->groupBy(function ($value, $key) {
            return $value->payment_at->format("Y-m");
        });

        foreach ($bookingRevenues as $key => $value) {
            $month = (int) explode("-", $key)[1];
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

    private function revenueMonthChart($payments, $year, $month) {
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
}
