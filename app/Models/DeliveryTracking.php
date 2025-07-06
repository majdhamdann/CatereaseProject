<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $delivery_id
 * @property int $delivery_person_id
 * @property string $latitude
 * @property string $longitude
 * @property string $recorded_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Delivery $delivery
 * @property-read \App\Models\DeliveryPerson $deliveryPerson
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryTracking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryTracking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryTracking query()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryTracking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryTracking whereDeliveryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryTracking whereDeliveryPersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryTracking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryTracking whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryTracking whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryTracking whereRecordedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryTracking whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DeliveryTracking extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $fillable = ['delivery_id', 'delivery_person_id', 'latitude', 'longitude', 'recorded_at'];


    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function deliveryPerson()
    {
        return $this->belongsTo(DeliveryPerson::class);
    }
}
