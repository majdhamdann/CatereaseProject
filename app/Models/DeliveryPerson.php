<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $vehicle_type
 * @property int $is_available
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Delivery> $deliveries
 * @property-read int|null $deliveries_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DeliveryTracking> $tracking
 * @property-read int|null $tracking_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryPerson newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryPerson newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryPerson query()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryPerson whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryPerson whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryPerson whereIsAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryPerson whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryPerson whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryPerson whereVehicleType($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Feedback> $feedbacks
 * @property-read int|null $feedbacks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Feedback> $allFeedbacks
 * @property-read int|null $all_feedbacks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Branch> $branches
 * @property-read int|null $branches_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Feedback> $complaint
 * @property-read int|null $complaint_count
 * @mixin \Eloquent
 */
class DeliveryPerson extends Model
{
    use HasFactory;
    protected $guarded = ['id'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'delivery_person_id');
    }

    public function tracking()
    {
        return $this->hasMany(DeliveryTracking::class);
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
    )->where('feedback_types.target_type', 'delivery_person')
     ->where('feedback.type', 'rating');
}

    public function complaint(){

    return $this->hasManyThrough(
        Feedback::class,
        FeedbackType::class,
        'target_ref_id',
        'FeedbackType_id',
        'id',
        'id'
    )->where('feedback_types.target_type', 'delivery_person')
     ->where('feedback.type', 'complaint');
}

    public function allFeedbacks()
{
    return $this->hasManyThrough(
        Feedback::class,
        FeedbackType::class,
        'target_ref_id',
        'feedbackType_id',
        'id',
        'id'
    )->where('feedback_types.target_type', 'delivery_person');
}

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'delivery_branch', 'delivery_person_id', 'branch_id');
    }





}
