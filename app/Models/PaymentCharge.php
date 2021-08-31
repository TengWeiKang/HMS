<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentCharge extends Model
{
    use HasFactory;
    public $table = "payment_charge";
    public $timestamps = false;

    protected $fillable = [
        'payment_id',
        'description',
        'price',
    ];
}
