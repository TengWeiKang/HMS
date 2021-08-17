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
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $visible = [
        "start_date",
        "end_date",
    ];


    public function room() {
        return $this->belongsTo(Room::class, "room_id");
    }

    public function reservable() {
        return $this->morphTo();
    }
}
