<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalysisController extends Controller
{
    public function __construct()
    {
        $this->middleware("customer");
    }

    public function index() {
        $payments = Payment::whereHas("reservation", function ($query){
            $query->where("customer_id", Auth::id());
        })->get();
        $years = $payments->groupBy(function ($value) {
            return $value->payment_at->format("Y");
        })->keys();

        return view("customer.analysis.index", ["years" => $years]);
    }

    public function json(Request $request) {
        $year = $request->year;

        // spent chart
        $json["spentChart"]["bookings"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $json["spentChart"]["services"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $json["spentChart"]["charges"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

        $reservations = Reservation::with("rooms")->where("status", 0)->where("customer_id", Auth::id())->get()
            ->filter(function ($reservation) use ($year) {
                return $reservation->created_at->format("Y") == $year;
            })->groupBy(function($reservation) {
            return $reservation->created_at->format("Y-m");
        });

        $payments = Payment::with("reservation", "reservation.customer", "items", "charges", "rooms")->whereHas("reservation", function ($query){
            $query->where("customer_id", Auth::id());
        })->get();

        $payments = $payments->filter(function ($value) use ($year) {
            return $value->payment_at->format("Y") == $year;
        });

        $spents = $payments->groupBy(function ($value) {
            return $value->payment_at->format("Y-m");
        });

        foreach ($spents as $key => $value) {
            $month = (int) explode("-", $key)[1] - 1;
            $json["spentChart"]["bookings"][$month] = $value->sum(function ($payment) {
                return $payment->bookingPriceWithDiscount();
            });
            $json["spentChart"]["services"][$month] = $value->sum(function ($payment) {
                return $payment->totalItemPricesWithDiscount();
            });
            $json["spentChart"]["charges"][$month] = $value->sum(function ($payment) {
                return $payment->totalChargesPrice();
            });
        }
        foreach ($reservations as $key => $value) {
            $month = (int) explode("-", $key)[1] - 1;
            $json["spentChart"]["charges"][$month] += $value->sum(function ($reservation) {
                return $reservation->deposit;
            });
        }

        // booking chart
        $json["bookingChart"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

        $bookings = $payments->groupBy(function ($value) {
            return $value->start_date->format("Y-m");
        });
        $bookings = $bookings->map(function($payments) {
            return $payments->count();
        });

        foreach ($bookings as $key => $value) {
            $month = (int) explode("-", $key)[1] - 1;
            $json["bookingChart"][$month] = $value;
        }
        return $json;
    }

    public function testInput() {
        $request = new Request();
        $request->setMethod("POST");
        $request->request->add(["year" => 2021]);
        dd($this->json($request));
    }
}
