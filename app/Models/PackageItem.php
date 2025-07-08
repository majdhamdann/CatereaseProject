<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageItem extends Model
{
    use HasFactory;
     protected $guarded = ['id'];
     public function package() {
        return $this->belongsTo(Package::class);
    }

    public function foodItem() {
        return $this->belongsTo(FoodItem::class);
    }
}
