<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Room;
use Carbon\Carbon;

class Reservation extends Model
{
    use HasFactory;

    public $table = "reservation";
    public $guarded = [];

    public $timestamps = ["created_at"];
    const UPDATED_AT = null;

    protected $fillable = [
        'customer_id',
        'start_date',
        'end_date',
        'check_in',
        'check_out',
        'deposit'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'created_at' => 'datetime',
    ];

    const DEPOSIT = 100;

    const STATUS = [
        0 => ["status" => "Waiting for Check-in", "color" => "mediumvioletred"],
        1 => ["status" => "Checked-in", "color" => "orangered"],
        2 => ["status" => "Completed", "color" => "mediumspringgreen"],
        3 => ["status" => "Cancelled", "color" => "darkgray"],
    ];

    /**
     * The rooms that belong to the Reservation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function rooms()
    {
        return $this->belongsToMany(Room::class, "reservation_room", "reservation_id", "room_id");
    }

    public function customer() {
        return $this->belongsTo(Customer::class, "customer_id");
    }

    /**
     * The services that belong to the Reservation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'room_service', 'reservation_id', 'service_id')->withPivot("quantity", "created_at");
    }

    public function id() {
        return "#" . sprintf("%06d", $this->id);
    }

    public function totalServicePrices()
    {
        $totalPrice = $this->services->sum(function ($service) {
            return $service->price * $service->pivot->quantity;
        });
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
        else if ($this->check_out == null) // occupied
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
        return $this->dateDifference() * $this->room->type->price;
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

    public function canCheckIn() {
        $today = Carbon::today();
        return $this->start_date <= $today && $this->end_date >= $today;
    }
}
