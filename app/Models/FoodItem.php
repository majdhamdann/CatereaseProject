<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodItem extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'food_item_id');
    }
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
        'target_ref_id',       // Foreign key on feedback_types
        'FeedbackType_id',     // Foreign key on feedback
        'id',                  // Local key on food_items
        'id'                   // Local key on feedback_types
    )->where('target_type', 'food_item');
}


}
