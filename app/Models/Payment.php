<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PaymentItem;

class Payment extends Model
{
    use HasFactory;

    public $table = "payment";
    const CREATED_AT = "payment_at";
    const UPDATED_AT = null;

    public $timestamps = false;

    protected $fillable = [
        'room',
        'reservable_type',
        'reservable_id',
        'price_per_night',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'payment_at' => 'datetime',
    ];

    /**
     * Get all of the items for the Payment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(PaymentItem::class, 'payment_id');
    }

    public function dateDifference() {
        return $this->end_date->diffInDays($this->start_date) + 1;
    }

    public function bookingPrice() {
        return $this->dateDifference() * $this->price_per_night;
    }

    public function finalPrices() {
        return $this->bookingPrice() + $this->totalServicePrices();
    }

    public function totalItemPrices()
    {
        $totalPrice = 0;
        foreach ($this->items as $item) {
            $totalPrice += $item->quantity * $item->pivot->unit_price;
        }
        return $totalPrice;
    }
}
