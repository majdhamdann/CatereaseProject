<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read \App\Models\OrderDetail|null $orderDetail
 * @property-read \App\Models\BranchServiceType|null $service
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItemService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItemService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItemService query()
 * @property int $id
 * @property int $order_detail_id
 * @property int $branch_service_type_id
 * @property string|null $custom_price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItemService whereBranchServiceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItemService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItemService whereCustomPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItemService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItemService whereOrderDetailId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItemService whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OrderItemService extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'order_item_service_map';

    protected $fillable = [
        'order_detail_id',
        'branch_service_type_id',
        'custom_price',
    ];

    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class);
    }

    public function service()
    {
        return $this->belongsTo(BranchServiceType::class, 'branch_service_type_id');
    }

}
