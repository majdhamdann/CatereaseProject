<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $branch_id
 * @property int $city_id
 * @property string $delivery_price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Branch $branch
 * @property-read \App\Models\City $city
 * @method static \Illuminate\Database\Eloquent\Builder|BranchDeliveryArea newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BranchDeliveryArea newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BranchDeliveryArea query()
 * @method static \Illuminate\Database\Eloquent\Builder|BranchDeliveryArea whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BranchDeliveryArea whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BranchDeliveryArea whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BranchDeliveryArea whereDeliveryPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BranchDeliveryArea whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BranchDeliveryArea whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BranchDeliveryArea extends Model
{
    use HasFactory;

   protected $guarded = ['id'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function district()
    {
        return $this->belongsTo(District::class);
    }

   
    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
