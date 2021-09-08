<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Reservation;
use App\Models\Payment;

class Guest extends Model
{
    use HasFactory;

    public $table = "guest";
    public $timestamps = false;

    public $fillable = [
        "username",
        "phone"
    ];

    public function reservations() {
        return $this->morphMany(Reservation::class, "reservable");
    }
    public function payments() {
        return $this->morphMany(Payment::class, "reservable");
    }
}
