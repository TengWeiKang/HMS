<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservable_type',
        'reservable_id',
        'room_id',
        'start_date',
        'end_date',
        'check_in',
        'check_out'
    ];
}
