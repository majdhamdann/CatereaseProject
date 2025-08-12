<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @property int $id
 * @property int $branch_id
 * @property int $service_type_id
 * @property string|null $custom_price
 * @property string $service_cost
 * @property int $is_available
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Branch $branch
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderServiceType> $orderServiceTypes
 * @property-read int|null $order_service_types_count
 * @property-read \App\Models\ServiceType $serviceType
 * @method static \Illuminate\Database\Eloquent\Builder|BranchServiceType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BranchServiceType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BranchServiceType query()
 * @method static \Illuminate\Database\Eloquent\Builder|BranchServiceType whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BranchServiceType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BranchServiceType whereCustomPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BranchServiceType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BranchServiceType whereIsAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BranchServiceType whereServiceCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BranchServiceType whereServiceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BranchServiceType whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Package> $packagesWithExtra
 * @property-read int|null $packages_with_extra_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CartItem> $cartItems
 * @property-read int|null $cart_items_count
 * @mixin \Eloquent
 */
class BranchServiceType extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }


//    public function orderServiceTypes()
//    {
//        return $this->hasMany(OrderServiceType::class, 'branch_service_type_id');
//    }



    public function orderServiceTypes()
    {
        return $this->hasMany(OrderServiceType::class, 'branch_service_type_id');
    }

    public function packagesWithExtra()
    {
        return $this->belongsToMany(
            Package::class,
            'package_branch_service_map',
            'branch_service_type_id',
            'package_id'
        )->withTimestamps();
    }

    public function cartItems()
    {
        return $this->belongsToMany(CartItem::class, 'cart_item_service_map')
            ->withPivot('custom_price')
            ->withTimestamps();
    }

}
