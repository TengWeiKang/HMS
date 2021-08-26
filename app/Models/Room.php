<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Facility;
use App\Models\Reservation;

class Room extends Model
{
    use HasFactory;

    const STATUS = [
        0 => ["status" => "Available", "color" => "#0F0"],
        1 => ["status" => "Closed", "color" => "#111"],
        2 => ["status" => "Dirty", "color" => "#AAA"]
    ];

    public $table = "room";

    public $timestamps = ["created_at"];
    const UPDATED_AT = null;

    protected $fillable = [
        'room_id',
        'name',
        'price',
        'single_bed',
        'double_bed',
        'room_image',
        'image_type',
        'note',
        'housekeptBy',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function status() {
        if ($this->isReserved()) {
            return "Reserved";
        }
        $additional = "";
        if (($this->status == 2 && $this->housekeptBy != null)) {
            $additional = "\n(Assigned: " . ($this->housekept->username) .")";
        }
        return self::STATUS[$this->status]["status"] . $additional;
    }

    public function statusColor() {
        if ($this->isReserved()) {
            return "orange";
        }
        return self::STATUS[$this->status]["color"];
    }

    /**
     * The facilities that belong to the Room
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function facilities()
    {
        return $this->belongsToMany(Facility::class, "room_facility", "room_id", "facility_id");
    }
    /**
     * Get all of the reservations for the Room
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'room_id');
    }

    public function histories()
    {
        return $this->hasMany(Reservation::class, 'room_id')->whereNotNull("check_out")->orderBy("start_date", "DESC");
    }

    public function housekept()
    {
        return $this->belongsTo(Employee::class, "housekeptBy");
    }

    public function isReserved() {
        return $this->reservations->filter(function ($value, $key) {
            return $value->check_in != null && $value->check_out == null;
        })->count() > 0;
    }
}
