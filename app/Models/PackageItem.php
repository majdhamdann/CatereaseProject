<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property int $package_id
 * @property int $food_item_id
 * @property int $quantity
 * @property int $is_optional
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\FoodItem $foodItem
 * @property-read \App\Models\Package $package
 * @method static \Illuminate\Database\Eloquent\Builder|PackageItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageItem whereFoodItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageItem whereIsOptional($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageItem wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PackageItem extends Model
{
    use HasFactory;
     protected $guarded = ['id'];

     public function package() {
        return $this->belongsTo(Package::class);
    }

    public function foodItem() {
        return $this->belongsTo(FoodItem::class);
    }
}
