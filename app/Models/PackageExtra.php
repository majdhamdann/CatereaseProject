<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $package_id
 * @property string $name
 * @property string $price
 * @property int $is_optional
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Package $package
 * @method static \Illuminate\Database\Eloquent\Builder|PackageExtra newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageExtra newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageExtra query()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageExtra whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageExtra whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageExtra whereIsOptional($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageExtra whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageExtra wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageExtra wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageExtra whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PackageExtra extends Model
{
    use HasFactory;
     protected $guarded = ['id'];
     public function package() {
        return $this->belongsTo(Package::class);
    }
}
