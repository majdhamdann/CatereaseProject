<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $cart_id
 * @property int $package_id
 * @property int $quantity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Cart $cart
 * @property-read \App\Models\Package $package
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereCartId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CartPackageExtra> $packageExtras
 * @property-read int|null $package_extras_count
 * @property string $total_price
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereTotalPrice($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CartPackageExtra> $extras
 * @property-read int|null $extras_count
 * @property int $extra_persons
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereExtraPersons($value)
 * @property int|null $occasion_type_id
 * @property-read \App\Models\OccasionType|null $occasionType
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BranchServiceType> $services
 * @property-read int|null $services_count
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereOccasionTypeId($value)
 * @mixin \Eloquent
 */
class CartItem extends Model
{
    use HasFactory;
     protected $guarded = ['id'];
    public function cart() {
        return $this->belongsTo(Cart::class, 'cart_id');
    }
//    public function cart() {
//        return $this->belongsTo(Cart::class);
//    }

    public function package() {
        return $this->belongsTo(Package::class);
    }
//    public function packageExtras()
//    {
//        return $this->hasMany(CartPackageExtra::class);
//    }
    public function extras()
    {
        return $this->hasMany(CartPackageExtra::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function packageExtras()
    {
        return $this->hasMany(CartPackageExtra::class, 'cart_item_id')->with('extra');
    }
    public function services()
    {
        return $this->belongsToMany(BranchServiceType::class, 'cart_item_service_map')
            ->withPivot('custom_price')
            ->withTimestamps();
    }
    public function occasionType()
    {
        return $this->belongsTo(OccasionType::class);
    }

}
