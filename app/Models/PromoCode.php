<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    use HasFactory;
     protected $guarded = ['id'];
      public function orders() {
        return $this->hasMany(Order::class, 'promo_code_id');
    }

    public function coupons() {
        return $this->hasMany(Coupon::class, 'promo_code_id');
    }
}
