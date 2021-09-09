<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PaymentItem;
use App\Models\PaymentCharge;

class Payment extends Model
{
    use HasFactory;

    public $table = "payment";
    const CREATED_AT = "payment_at";
    const UPDATED_AT = null;

    public $timestamps = false;

    protected $fillable = [
        'reservation_id',
        'room_id',
        'room_name',
        'reservable_type',
        'reservable_id',
        'price_per_night',
        'start_date',
        'end_date',
        'discount',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'payment_at' => 'datetime',
    ];
    /**
     * Get the reservation that owns the Payment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }

    /**
     * Get all of the items for the Payment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(PaymentItem::class, 'payment_id');
    }

    public function charges()
    {
        return $this->hasMany(PaymentCharge::class, 'payment_id');
    }

    public function dateDifference() {
        return $this->end_date->diffInDays($this->start_date) + 1;
    }

    public function bookingPrice() {
        return $this->dateDifference() * $this->price_per_night;
    }

    public function totalChargesPrice() {
        $charges = 0;
        foreach ($this->charges as $paymentCharge) {
            $charges += $paymentCharge->price;
        }
        return $charges;
    }

    public function totalItemPrices()
    {
        $totalPrice = 0;
        foreach ($this->items as $item) {
            $totalPrice += $item->quantity * $item->unit_price;
        }
        return $totalPrice;
    }

    public function subPrice() {
        return $this->bookingPrice() + $this->totalItemPrices();
    }

    public function totalSubPrices() {
        return $this->subPrice() * (100 - $this->discount) / 100;
    }


    public function totalPrices() {
        return $this->totalSubPrices() + $this->totalChargesPrice();
    }

    public function reservable() {
        return $this->morphTo();
    }
}
