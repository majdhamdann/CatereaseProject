<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory whereUpdatedAt($value)
 * @property int $branch_id
 * @property int $category_id
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory whereCategoryId($value)
 * @property-read \App\Models\Branch $branch
 * @property-read \App\Models\Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FoodItem> $foodItems
 * @property-read int|null $food_items_count
 * @mixin \Eloquent
 */
class FoodCategory extends Model
{
    use HasFactory;
    protected $guarded = [];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // public function foodItems()
    // {
    //     return $this->hasMany(FoodItem::class);
    // }




}
