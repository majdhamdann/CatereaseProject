<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $photo
 * @property int $owner_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Branch> $branches
 * @property-read int|null $branches_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FeedbackType> $feedbackTypes
 * @property-read int|null $feedback_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Feedback> $feedbacks
 * @property-read int|null $feedbacks_count
 * @property-read \App\Models\User $owner
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant query()
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereUpdatedAt($value)
 * @property int $is_active
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereIsActive($value)
 * @mixin \Eloquent
 */
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
