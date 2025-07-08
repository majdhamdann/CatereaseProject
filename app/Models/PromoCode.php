<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $code
 * @property string $discount_percent
 * @property string|null $max_discount
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Coupon> $coupons
 * @property-read int|null $coupons_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereDiscountPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereMaxDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
