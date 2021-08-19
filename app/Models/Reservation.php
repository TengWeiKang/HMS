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

    public function room() {
        return $this->belongsTo(Room::class, "room_id");
    }

    public function reservable() {
        return $this->morphTo();
    }

    public function statusName() {
        $today = Carbon::today();
        if ($this->check_in == null)
            return "Waiting for Check-in";
        else if ($this->check_out == null)
            return "Checked-in";
        else
            return "Completed";
    }

    public function statusColor() {
        $today = Carbon::today();
        if ($this->check_in == null)
            return "yellow";
        else if ($this->check_out == null)
            return "orangered";
        else
            return "green";
    }

    public function dateDifference() {
        return $this->end_date->diffInDays($this->start_date) + 1;
    }

    public function bookingPrice() {
        return $this->dateDifference() * $this->room->price;
    }

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
