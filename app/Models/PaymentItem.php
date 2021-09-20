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
        'service_id',
        'service_name',
        'quantity',
        'unit_price',
        'purchase_at',
    ];

    protected $casts = [
        "purchase_at" => "datetime"
    ];

    public function servicePrice() {
        return $this->quantity * $this->unit_price;
    }

    /**
     * Get the payment that owns the PaymentItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    /**
     * Get the service that owns the PaymentItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function discountedPrice() {
        return $this->servicePrice() * (100 - $this->payment->discount) / 100;
    }
}
