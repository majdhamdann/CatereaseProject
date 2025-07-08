<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $branch_id
 * @property int $food_category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Branch $branch
 * @property-read \App\Models\FoodCategory $foodCategory
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FoodItem> $foodItems
 * @property-read int|null $food_items_count
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereFoodCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 * @property string $name
 * @property string|null $description
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Package> $packages
 * @property-read int|null $packages_count
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereName($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FoodCategory> $foodCategories
 * @property-read int|null $food_categories_count
 * @mixin \Eloquent
 */
class Category extends Model
{
    use HasFactory;
    protected $guarded = ['id'];


    public function foodCategories()
    {
        return $this->hasMany(FoodCategory::class);
    }

    public function packages() {
        return $this->belongsToMany(Package::class, 'package_categories', 'category_id', 'package_id');
    }

}
