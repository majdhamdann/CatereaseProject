<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $branch_id
 * @property string $day_of_week
 * @property string|null $open_time
 * @property string|null $close_time
 * @property int $is_closed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Branch $branch
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDay newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDay newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDay query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDay whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDay whereCloseTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDay whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDay whereDayOfWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDay whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDay whereIsClosed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDay whereOpenTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDay whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class WorkingDay extends Model
{
    use HasFactory;
     protected $guarded = ['id'];
     public function branch() {
        return $this->belongsTo(Branch::class);
    }
}
