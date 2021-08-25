<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentItem extends Model
{
    use HasFactory;

    public $table = "payment_item";
    public $timestamps = false;

    protected $fillable = [
        'payment_id',
        'service_name',
        'quantity',
        'unit_price',
    ];

    public function servicePrice() {
        return $this->quantity * $this->unit_price;
    }
}
