<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Facility;
use App\Models\Reservation;
use Carbon\Carbon;

class Room extends Model
{
    use HasFactory;

    const STATUS = [
        0 => ["status" => "Available", "color" => "#0f0"],
        1 => ["status" => "Booked", "color" => "#e22"],
        2 => ["status" => "Dirty", "color" => "#282828"],
        3 => ["status" => "Repairing", "color" => "#ff8484"],
        4 => ["status" => "Reserved", "color" => "orange"]
    ];

    public $table = "room";

    public $timestamps = ["created_at"];
    const UPDATED_AT = null;

    protected $fillable = [
        'room_id',
        'name',
        'room_type',
        'single_bed',
        'double_bed',
        'room_image',
        'image_type',
        'note',
        'housekeep_by',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function status() {
        if ($this->isReserved()) {
            return 4;
        }
        else if($this->status == 0 && $this->isBooked()) {
            return 1;
        }
        return $this->status;
    }

    public function statusName($withAssigned) {
        $additional = "";
        if ($withAssigned && $this->status() == 2 && $this->housekeeper != null) {
            $additional = "<br><small>(" . ($this->housekeeper->username) .")</small>";
        }
        return self::STATUS[$this->status()]["status"] . $additional;
    }

    public function statusColor() {
        return self::STATUS[$this->status()]["color"];
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

    /**
     * Get the room type that owns the Room
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(RoomType::class, 'room_type');
    }

    public function housekeeper()
    {
        return $this->belongsTo(Employee::class, "housekeep_by");
    }

    public function isReserved() {
        return $this->reservedBy() != null;
    }

    public function imageSrc() {
        if ($this->room_image == null)
            return $this->type->imageSrc();
        return "data:" . $this->image_type . ";base64," . base64_encode($this->room_image);
    }

    public function reservedBy() {
        $reservations = $this->reservations->filter(function ($value, $key) {
            return $value->check_in != null && $value->check_out == null;
        })->values();
        if ($reservations->count() > 0) {
            return $reservations[0];
        }
        return null;
    }

    public function isBooked() {
        $today = Carbon::today();
        $count = $this->reservations->filter(function ($value) use ($today) {
            return $value->status() == 0 && $value->start_date <= $today && $value->end_date >= $today;
        })->count();
        return $count > 0;
    }

    public function isTurnoverToday() {
        return $this->departure()->count() > 0 && $this->arrival()->count() > 0;
    }

    public function isDepartureToday() {
        return $this->departure()->count() > 0 && $this->arrival()->count() == 0;
    }

    public function isArrivalToday() {
        return $this->departure()->count() == 0 && $this->arrival()->count() > 0;
    }

    public function departure() {
        $today = Carbon::today();
        return $this->reservations->filter(function ($value, $key) use ($today) {
            return !is_null($value->check_in) && is_null($value->check_out) && $value->end_date->addDays() == $today && $value->status == 1;
        });
    }

    public function arrival() {
        $today = Carbon::today();
        return $this->reservations->filter(function ($value, $key) use ($today) {
            return is_null($value->check_in) && $value->start_date == $today && $value->status == 1;
        });
    }
}
