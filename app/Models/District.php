<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;
     protected $guarded = ['id'];

     public function city()
    {
        return $this->belongsTo(City::class);
    }
  
    public function areas()
    {
        return $this->hasMany(Area::class);
    }
}
