<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Room;

class Facility extends Model
{
    use HasFactory;

    public $table = "facility";
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function rooms()
    {
        return $this->belongsToMany(Room::class, "room_facility", "facility_id", "room_id");
    }
}
