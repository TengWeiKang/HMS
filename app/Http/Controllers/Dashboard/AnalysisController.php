<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AnalysisController extends Controller
{
    public function __construct()
    {
        $this->middleware("employee:admin");
    }

    public function index() {
        $roomTypes = RoomType::all();
        $payments = Payment::all();
        $years = $payments->groupBy(function ($value) {
            return $value->payment_at->format("Y");
        })->keys();
        $yearNow = Carbon::now()->year;
        $years = $years->unless($years->contains($yearNow), function ($collection) use ($yearNow) {
            return $collection->push($yearNow);
        })->sortDesc();
        return view("dashboard.analysis.index", ["roomTypes" => $roomTypes, "years" => $years]);
    }

    public function json(Request $request) {
        $json = [];
        $payments = Payment::with("items", "charges", "reservation", "reservation.room", "items.service")->get();
        $paymentItems = PaymentItem::with("payment", "payment.reservation.room", "service")->get();
        $servicesArray = Service::all()->pluck("name")->toArray();
        $rooms = Room::with("reservations")->get();

        $json["revenueYearChart"] = $this->revenueYearChart($payments, $request->year, $request->roomType);
        $json["revenueMonthChart"] = $this->revenueMonthChart($payments, $request->year, $request->month, $request->roomType);
        $json["roomStatusChart"] = $this->roomStatusChart($rooms, $request->roomType);
        $json["roomServiceChart"] = $this->roomServiceChart($paymentItems, $servicesArray, $request->year, $request->month, $request->roomType);
        return $json;
    }

    private function revenueYearChart($payments, $year, $roomType) {
        $json["bookings"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $json["services"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $json["charges"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $payments = $this->paymentsFilterByYear($payments, $year);
        $payments = $this->paymentsFilterByRoomType($payments, $roomType);
        $bookingRevenues = $this->paymentsGroupByMonth($payments);
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
        $payments = $this->paymentsFilterByYear($payments, $year);
        $payments = $this->paymentsFilterByRoomType($payments, $roomType);
        $info = $this->paymentsGroupByMonth($payments)->get($year . "-" . $month, collect());
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

    private function roomStatusChart($rooms, $roomType) {
        $rooms = $this->roomsFilterByRoomType($rooms, $roomType);
        $info = $rooms->groupBy(function ($value) {
            return $value->status();
        });
        $json = [
            optional($info->get(0))->count() ?? 0,
            optional($info->get(2))->count() ?? 0,
            optional($info->get(3))->count() ?? 0,
            optional($info->get(4))->count() ?? 0,
        ];
        return $json;
    }

    private function roomServiceChart($paymentItems, $services, $year, $month, $roomType) {
        $json["labels"] = $services;
        $json["items"] = [];
        $paymentItems = $this->paymentItemsFilterByYear($paymentItems, $year);
        $paymentItems = $this->paymentItemsFilterByRoomType($paymentItems, $roomType);
        $paymentItems = $this->paymentItemsGroupByMonthAndItem($paymentItems);
        foreach ($services as $service) {
            $key = $year . "-" . $month;
            array_push($json["items"], round(optional($paymentItems->get($key))->get($service) ?? 0, 2));
        }
        return $json;
    }

    private function paymentsFilterByYear($payments, $year) {
        return $payments->filter(function ($value) use ($year) {
            return Str::startsWith($value->payment_at, $year);
        });
    }

    private function paymentsGroupByMonth($payments) {
        return $payments->groupBy(function ($value) {
            return $value->payment_at->format("Y-m");
        });
    }

    private function paymentsFilterByRoomType($payments, $roomType) {
        if (!empty($roomType)) {
            $payments = $payments->filter(function ($value) use ($roomType) {
                return $value->reservation->room->room_type == $roomType;
            });
        }
        return $payments;
    }

    private function paymentItemsFilterByYear($paymentItems, $year) {
        return $paymentItems->filter(function ($paymentItem) use ($year) {
            return Str::startsWith($paymentItem->payment->payment_at, $year);
        });
    }

    private function paymentItemsGroupByMonthAndItem($paymentItems) {
        return $paymentItems->groupBy([function ($paymentItem) {
            return $paymentItem->payment->payment_at->format("Y-m");
        }, "service.name"])->map(function ($services) {
            return $services->map(function($paymentItem) {
                return $paymentItem->sum(function ($paymentItem) {
                    return $paymentItem->discountedPrice();
                });
            });
        });
    }

    private function paymentItemsFilterByRoomType($paymentItems, $roomType) {
        if (!empty($roomType)) {
            $paymentItems = $paymentItems->filter(function ($paymentItem) use ($roomType) {
                return $paymentItem->payment->reservation->room->room_type == $roomType;
            });
        }
        return $paymentItems;
    }

    private function roomsFilterByRoomType($rooms, $roomType) {
        if (!empty($roomType)) {
            $rooms = $rooms->filter(function ($value) use ($roomType) {
                return $value->room_type == $roomType;
            });
        }
        return $rooms;
    }
}
