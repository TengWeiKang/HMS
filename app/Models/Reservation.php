<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Room;

class Reservation extends Model
{
    use HasFactory;

    public $table = "reservation";

    public $timestamps = ["created_at"];
    const UPDATED_AT = null;

    protected $fillable = [
        'room_id',
        'name',
        'price',
        'single_bed',
        'double_bed',
        'image_type',
        'room_image',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function room() {
        return $this->hasOne(Room::class, "room_id");
    }
}
