<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    public $table = "service";
    public $timestamps = false;

    protected $fillable = [
        'name',
        'price',
    ];

    /**
     * Get all of the paymentItem for the Service
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paymentItems()
    {
        return $this->hasMany(PaymentItem::class, 'service_id');
    }
}
