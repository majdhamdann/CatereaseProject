<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $FeedbackType_id
 * @property string $type
 * @property string|null $score
 * @property string|null $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\FeedbackType $feedbackType
 * @property-read Model|\Eloquent $target
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback query()
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereFeedbackTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereUserId($value)
 * @property string|null $status
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereStatus($value)
 * @mixin \Eloquent
 */
class Feedback extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function feedbackType()
    {
        return $this->belongsTo(FeedbackType::class, 'FeedbackType_id');
    }

    public function target()
    {
        return $this->morphTo(__FUNCTION__, 'target_type', 'target_ref_id');
    }
}
