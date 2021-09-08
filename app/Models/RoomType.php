<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;
    public $table = "room_type";

    public $timestamps = false;
    const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'single_bed',
        'double_bed'
    ];

    /**
     * Get all of the rooms for the RoomType
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rooms()
    {
        return $this->hasMany(Room::class, 'room_type');
    }
}
