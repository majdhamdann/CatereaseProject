<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackType extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'FeedbackType_id');
    }

    // ⬅️ العلاقة الديناميكية مع الهدف
    public function target()
    {
        return $this->morphTo(__FUNCTION__, 'target_type', 'target_ref_id');
    }
}
