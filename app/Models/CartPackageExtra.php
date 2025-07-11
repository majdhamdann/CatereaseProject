<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartPackageExtra extends Model
{
    use HasFactory;
     protected $guarded = ['id'];

    public function cartItem()
    {
        return $this->belongsTo(CartItem::class);
    }
    public function extra()
    {
        return $this->belongsTo(PackageExtra::class, 'extra_id');
    }
}
