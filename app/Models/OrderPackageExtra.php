<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $order_detail_id
 * @property int $extra_id
 * @property int $quantity
 * @property string $unit_price
 * @property string $total_price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\PackageExtra $extra
 * @property-read \App\Models\OrderDetail $orderDetail
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackageExtra newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackageExtra newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackageExtra query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackageExtra whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackageExtra whereExtraId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackageExtra whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackageExtra whereOrderDetailId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackageExtra whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackageExtra whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackageExtra whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPackageExtra whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OrderPackageExtra extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class, 'order_detail_id');
    }

    public function extra()
    {
        return $this->belongsTo(PackageExtra::class, 'extra_id');
    }
}
