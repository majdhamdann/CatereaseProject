<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $package_id
 * @property string $value
 * @property string $description
 * @property string $start_at
 * @property string $end_at
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Package $package
 * @method static \Illuminate\Database\Eloquent\Builder|PackageDiscount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageDiscount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageDiscount query()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageDiscount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageDiscount whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageDiscount whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageDiscount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageDiscount whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageDiscount wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageDiscount whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageDiscount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageDiscount whereValue($value)
 * @mixin \Eloquent
 */
class PackageDiscount extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
