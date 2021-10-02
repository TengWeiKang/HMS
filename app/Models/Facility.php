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
        'default'
    ];

    public function roomTypes() {
        return $this->belongsToMany(RoomType::class, "room_type_facility", "facility_id", "room_type_id");
    }
}
