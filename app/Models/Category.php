<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function foodItems()
    {
        return $this->hasMany(FoodItem::class, 'category_id');
    }
}
