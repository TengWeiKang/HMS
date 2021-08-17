<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Reservation;

class Guest extends Model
{
    use HasFactory;

    public $table = "guest";
    public $timestamps = false;

    public $fillable = [
        "name"
    ];

    public function reservations() {
        return $this->morphMany(Reservation::class, "reservable");
    }
}
