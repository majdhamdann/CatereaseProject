<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;
     protected $guarded = ['id'];
    public function items() {
        return $this->hasMany(PackageItem::class);
    }

    public function extras() {
        return $this->hasMany(PackageExtra::class);
    }

    public function categories() {
        return $this->belongsToMany(Category::class, 'package_categories', 'package_id', 'category_id');
    }
}
