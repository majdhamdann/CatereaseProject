<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
     public function branch() {
        return $this->belongsTo(Branch::class);
    }

    public function promoCode() {
        return $this->belongsTo(PromoCode::class, 'promo_code_id');
    }
}
