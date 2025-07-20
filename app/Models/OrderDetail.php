<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $order_id
 * @property int $food_item_id
 * @property int $quantity
 * @property string $unit_price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\FoodItem $foodItem
 * @property-read \App\Models\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetail whereFoodItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetail whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetail whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetail whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetail whereUpdatedAt($value)
 * @property int $package_id
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetail wherePackageId($value)
 * @property-read \App\Models\Package $package
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderPackageExtra> $extras
 * @property-read int|null $extras_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BranchServiceType> $services
 * @property-read int|null $services_count
 * @property int|null $occasion_type_id
 * @property int $extra_persons
 * @property-read \App\Models\OccasionType|null $occasionType
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetail whereExtraPersons($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetail whereOccasionTypeId($value)
 * @mixin \Eloquent
 */
class OrderDetail extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

//    public function foodItem()
//    {
//        return $this->belongsTo(FoodItem::class, 'food_item_id');
//    }
    public function package()
    {
        return $this->belongsTo(Package::class);
    }
    public function foodItem()
   {
      return $this->belongsTo(FoodItem::class, 'food_item_id');
    }

    public function extras()
    {
        return $this->hasMany(OrderPackageExtra::class, 'order_detail_id');
    }
//    public function services()
//    {
//        return $this->belongsToMany(BranchServiceType::class, 'order_item_service_map')
//            ->withPivot('custom_price');
//    }

    public function services()
    {
        return $this->hasMany(OrderItemService::class, 'order_detail_id');
    }
    public function occasionType()
    {
        return $this->belongsTo(OccasionType::class, 'occasion_type_id');
    }


}
