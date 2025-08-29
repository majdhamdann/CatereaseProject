<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



/**
 * @property int $id
 * @property int $user_id
 * @property int $city_id
 * @property string|null $street
 * @property string|null $building
 * @property string|null $floor
 * @property string|null $apartment
 * @property string|null $coordinate
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\City $city
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Address newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Address newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Address query()
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereApartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereBuilding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereCoordinate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereFloor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereUserId($value)
 * @property string|null $latitude
 * @property string|null $longitude
 * @property int $is_default
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereLongitude($value)
 * @property int|null $district_id
 * @property int|null $area_id
 * @property-read \App\Models\Area|null $area
 * @property-read \App\Models\District|null $district
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereAreaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereDistrictId($value)
 * @mixin \Eloquent
 */
class Address extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'address_id');
    }
}
