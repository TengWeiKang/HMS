<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Room;

class Reservation extends Model
{
    use HasFactory;

    public $table = "reservation";
    public $guarded = [];

    public $timestamps = ["created_at"];
    const UPDATED_AT = null;

    protected $fillable = [
        'reservable_type',
        'reservable_id',
        'room_id',
        'start_date',
        'end_date',
        'check_in',
        'check_out'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    const STATUS = [
        0 => ["status" => "Waiting for Check-in", "color" => "mediumvioletred"],
        1 => ["status" => "Checked-in", "color" => "orangered"],
        2 => ["status" => "Completed", "color" => "mediumspringgreen"],
        3 => ["status" => "Cancelled", "color" => "darkgray"],
    ];

    public function room() {
        return $this->belongsTo(Room::class, "room_id");
    }

    public function reservable() {
        return $this->morphTo();
    }

    /**
     * The services that belong to the Reservation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'room_service', 'reservation_id', 'service_id')->withPivot("quantity");
    }

    public function totalServicePrices()
    {
        $totalPrice = 0;
        foreach ($this->services as $service) {
            $totalPrice += $service->price * $service->pivot->quantity;
        }
        return $totalPrice;
    }

    public function finalPrices() {
        return $this->bookingPrice() + $this->totalServicePrices();
    }

    public function status() {
        if ($this->status == 0) // cancelled
            return 3;
        if ($this->check_in == null) // awaiting to check in
            return 0;
        else if ($this->check_out == null) // checked in
            return 1;
        else // completed
            return 2;
    }

    public function statusName() {
        return self::STATUS[$this->status()]["status"];
    }

    public function statusColor() {
        return self::STATUS[$this->status()]["color"];
    }

    public function dateDifference() {
        return $this->end_date->diffInDays($this->start_date) + 1;
    }

    public function bookingPrice() {
        return $this->dateDifference() * $this->room->price;
    }

    /**
     * Get the payment associated with the Reservation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function payment()
    {
        return $this->hasOne(Payment::class, 'reservation_id');
    }

    // public function customerRedirect() {
    //     if ($this->reservable == null || $this->reservable_type instanceof Guest) {
    //         return "";
    //     }
    //     return "";
    // }

    // public function statusName() {
    //     $today = Carbon::today();
    //     if ($this->check_in == null) {
    //         if ($today < $this->start_date)
    //             return "Waiting for Check-in";
    //         else if ($today == $this->start_date)
    //             return "Check-in Today";
    //         else
    //             return "Check-in Over " . $today->diffInDays($this->start_date) . " day(s)";
    //     }
    //     else if ($this->check_out == null) {
    //         if ($today < $this->end_date)
    //             return "Check-out in " . $today->diffInDays($this->end_date) . " days";
    //         else if ($today == $startDate)
    //             return "Check-out by today";
    //         else
    //             return "Check-out over " . $today->diffInDays($this->end_date) . " days";
    //     }
    //     else
    //         return "Completed";
    // }
}
