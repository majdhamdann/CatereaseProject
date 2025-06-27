<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



/**
 *
 *
 * @property int $id
 * @property int $Restaurant_id
 * @property string $location
 * @property string|null $description
 * @property string|null $photo
 * @property int|null $Manager_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FoodItem> $foodItems
 * @property-read int|null $food_items_count
 * @property-read \App\Models\User|null $manager
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \App\Models\Restaurant|null $restaurant
 * @method static \Illuminate\Database\Eloquent\Builder|Branch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Branch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Branch query()
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereManagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereRestaurantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereUpdatedAt($value)
 * @property int $restaurant_id
 * @property int|null $manager_id
 * @property int|null $city_id
 * @property string|null $location_note
 * @property string|null $latitude
 * @property string|null $longitude
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereLocationNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereLongitude($value)
 * @mixin \Eloquent
 */
class Branch extends Model
{
    use HasFactory;
    protected $guarded = ['id'];






    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function foodItems()
    {
        return $this->hasMany(FoodItem::class, 'branch_id');
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'branch_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'branch_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
