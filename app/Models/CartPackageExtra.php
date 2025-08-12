<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $cart_item_id
 * @property int $extra_id
 * @property int $quantity
 * @property string $total_price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CartItem $cartItem
 * @property-read \App\Models\PackageExtra $extra
 * @method static \Illuminate\Database\Eloquent\Builder|CartPackageExtra newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CartPackageExtra newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CartPackageExtra query()
 * @method static \Illuminate\Database\Eloquent\Builder|CartPackageExtra whereCartItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartPackageExtra whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartPackageExtra whereExtraId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartPackageExtra whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartPackageExtra whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartPackageExtra whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartPackageExtra whereUpdatedAt($value)
 * @property string $unit_price
 * @method static \Illuminate\Database\Eloquent\Builder|CartPackageExtra whereUnitPrice($value)
 * @mixin \Eloquent
 */
class CartPackageExtra extends Model
{
    use HasFactory;
     protected $guarded = ['id'];

    public function cartItem()
    {
        return $this->belongsTo(CartItem::class);
    }

    public function extra()
    {
        return $this->belongsTo(PackageExtra::class, 'extra_id');
    }

}
