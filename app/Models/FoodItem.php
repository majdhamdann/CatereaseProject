<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $branch_id
 * @property int $category_id
 * @property string $name
 * @property string|null $description
 * @property string $price
 * @property string|null $discount_price
 * @property string|null $image_url
 * @property int $available
 * @property int|null $calories
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Branch $branch
 * @property-read \App\Models\Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FeedbackType> $feedbackTypes
 * @property-read int|null $feedback_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Feedback> $feedbacks
 * @property-read int|null $feedbacks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderDetail> $orderDetails
 * @property-read int|null $order_details_count
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem whereAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem whereCalories($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem whereDiscountPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem whereUpdatedAt($value)
 * @property string|null $photo
 * @property string|null $type
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem whereType($value)
 * @property int $food_category_id
 * @property-read \App\Models\FoodCategory $foodCategory
 * @method static \Illuminate\Database\Eloquent\Builder|FoodItem whereFoodCategoryId($value)
 * @mixin \Eloquent
 */
class FoodItem extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function foodCategory()
    {
        return $this->belongsTo(FoodCategory::class, 'food_category_id');
    }

//    public function orderDetails()
//    {
//        return $this->hasMany(OrderDetail::class, 'food_item_id');
//    }
    public function feedbackTypes()
{
    return $this->hasMany(FeedbackType::class, 'target_ref_id')
                ->where('target_type', 'food_item');
}

    public function feedbacks()
{
    return $this->hasManyThrough(
        Feedback::class,
        FeedbackType::class,
        'target_ref_id',
        'FeedbackType_id',
        'id',
        'id'
    )->where('target_type', 'food_item');
}




}
