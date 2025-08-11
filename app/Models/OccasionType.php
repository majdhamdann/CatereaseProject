<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Package> $packages
 * @property-read int|null $packages_count
 * @method static \Illuminate\Database\Eloquent\Builder|OccasionType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OccasionType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OccasionType query()
 * @method static \Illuminate\Database\Eloquent\Builder|OccasionType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OccasionType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OccasionType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OccasionType whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OccasionType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OccasionType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OccasionType extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_occasion_map', 'occasion_type_id', 'package_id');
    }

}
