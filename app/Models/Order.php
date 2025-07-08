<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $branch_id
 * @property int|null $delivery_id
 * @property string $status
 * @property int|null $promo_code_id
 * @property string $total_price
 * @property int|null $address_id
 * @property int|null $cart_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Address|null $address
 * @property-read \App\Models\Branch $branch
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderDetail> $orderDetails
 * @property-read int|null $order_details_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCartId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeliveryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePromoCodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUserId($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderServiceType> $orderServiceTypes
 * @property-read int|null $order_service_types_count
 * @mixin \Eloquent
 */
class Order extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function delivery() {
        return $this->hasOne(Delivery::class);
    }


    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
    public function orderServiceTypes()
    {
        return $this->hasMany(OrderServiceType::class, 'order_id');
    }
    public function services() {
        return $this->hasMany(OrderServiceType::class);
    }

    public function bill() {
        return $this->hasOne(Bill::class);
    }

    public function cart() {
        return $this->belongsTo(Cart::class, 'cart_id');
    }

    public function promoCode() {
        return $this->belongsTo(PromoCode::class, 'promo_code_id');
    }
}
