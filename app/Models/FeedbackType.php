<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $target_type
 * @property int $target_ref_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Feedback> $feedbacks
 * @property-read int|null $feedbacks_count
 * @property-read Model|\Eloquent $target
 * @method static \Illuminate\Database\Eloquent\Builder|FeedbackType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeedbackType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeedbackType query()
 * @method static \Illuminate\Database\Eloquent\Builder|FeedbackType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeedbackType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeedbackType whereTargetRefId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeedbackType whereTargetType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeedbackType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FeedbackType extends Model
{
    use HasFactory;
    protected $guarded = ['id'];


    public function target()
    {
        return $this->morphTo(__FUNCTION__, 'target_type', 'target_ref_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'FeedbackType_id');
    }
    public function branch1()
    {
        if ($this->target_type === 'package') {
            return $this->belongsTo(Package::class, 'target_ref_id')->with('branch');
        }

        if ($this->target_type === 'delivery_person') {
            return $this->belongsTo(DeliveryPerson::class, 'target_ref_id')->with('branches');
        }

        if ($this->target_type === 'branch') {
            return $this->belongsTo(Branch::class, 'target_ref_id');
        }

        return null;
    }
  public function package()
{
    return $this->belongsTo(Package::class, 'target_ref_id');
}

public function deliveryPerson()
{
    return $this->belongsTo(DeliveryPerson::class, 'target_ref_id');
}

public function branch()
{
    return $this->belongsTo(Branch::class, 'target_ref_id');
}


}
