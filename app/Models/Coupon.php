<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $branch_id
 * @property string $code
 * @property string $discount_amount
 * @property string $expiration_date
 * @property int $used
 * @property int|null $promo_code_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Branch $branch
 * @property-read \App\Models\PromoCode|null $promoCode
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon query()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereExpirationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon wherePromoCodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereUsed($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Package> $packages
 * @property-read int|null $packages_count
 * @property int|null $user_id
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereUserId($value)
 * @mixin \Eloquent
 */
class Coupon extends Model
{
    use HasFactory;
      protected $guarded = ['id'];

     public function branch() {
        return $this->belongsTo(Branch::class);
    }

    // public function promoCode() {
    //     return $this->belongsTo(PromoCode::class, 'promo_code_id');
    // }

    public function packages() {
        return $this->belongsToMany(Package::class, 'package_coupon');
    }

    public function user()
{
    return $this->belongsTo(User::class);
}

}
