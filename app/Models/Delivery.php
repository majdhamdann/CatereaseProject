<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;
     protected $guarded = ['id'];
    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function deliveryPerson() {
        return $this->belongsTo(DeliveryPerson::class);
    }

    public function tracking() {
        return $this->hasMany(DeliveryTracking::class, 'delivery_id');
    }
}
