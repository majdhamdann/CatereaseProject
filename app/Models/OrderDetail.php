<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function foodItem()
    {
        return $this->belongsTo(FoodItem::class, 'food_item_id');
    }
}
