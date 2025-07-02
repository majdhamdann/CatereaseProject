<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $order_id
 * @property int $branch_service_type_id
 * @property int $quantity
 * @property string $total_price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BranchServiceType $branchServiceType
 * @method static \Illuminate\Database\Eloquent\Builder|OrderServiceType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderServiceType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderServiceType query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderServiceType whereBranchServiceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderServiceType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderServiceType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderServiceType whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderServiceType whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderServiceType whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderServiceType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OrderServiceType extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function branchServiceType()
    {
        return $this->belongsTo(BranchServiceType::class, 'branch_service_type_id');
    }
}
