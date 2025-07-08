<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Model\DeliveryTracking;

/**
 *
 *
 * @property int $id
 * @property int $order_id
 * @property int|null $delivery_person_id
 * @property string $status
 * @property string|null $estimated_time
 * @property string|null $delivered_at
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\DeliveryPerson|null $deliveryPerson
 * @property-read \App\Models\Order $order
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DeliveryTracking> $tracking
 * @property-read int|null $tracking_count
 * @method static \Illuminate\Database\Eloquent\Builder|Delivery newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Delivery newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Delivery query()
 * @method static \Illuminate\Database\Eloquent\Builder|Delivery whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Delivery whereDeliveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Delivery whereDeliveryPersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Delivery whereEstimatedTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Delivery whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Delivery whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Delivery whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Delivery whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Delivery whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Delivery extends Model
{
    use HasFactory;
    protected $guarded = ['id'];





    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function deliveryPerson()
    {
        return $this->belongsTo(DeliveryPerson::class, 'delivery_person_id');

    }

    public function tracking()
    {
        return $this->hasMany(DeliveryTracking::class);
    }
}
