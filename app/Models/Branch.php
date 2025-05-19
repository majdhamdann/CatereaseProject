<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'Restaurant_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'Manager_id');
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
}
