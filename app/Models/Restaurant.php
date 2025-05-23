<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function branches()
    {
        return $this->hasMany(Branch::class, 'Restaurant_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
     public function feedbackTypes()
{
    return $this->hasMany(FeedbackType::class, 'target_ref_id')
                ->where('target_type', 'restaurant');
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
    )->where('target_type', 'restaurant');
}
}
