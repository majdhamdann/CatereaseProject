<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $country
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Address> $addresses
 * @property-read int|null $addresses_count
 * @method static \Illuminate\Database\Eloquent\Builder|City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City query()
 * @method static \Illuminate\Database\Eloquent\Builder|City whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Branch> $branches
 * @property-read int|null $branches_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BranchDeliveryArea> $deliveryBranches
 * @property-read int|null $delivery_branches_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\District> $districts
 * @property-read int|null $districts_count
 * @mixin \Eloquent
 */
class City extends Model
{
    use HasFactory;
    protected $guarded = ['id'];


    public function addresses()
    {
        return $this->hasMany(Address::class, 'city_id');
    }

    public function branches()
    {
        return $this->hasMany(Branch::class, 'city_id');
    }

    public function deliveryBranches()
    {
      return $this->hasMany(BranchDeliveryArea::class);
    }
    public function districts()
    {
        return $this->hasMany(District::class);
    }

}
