<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OccasionType extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
     public function packages() {
        return $this->hasMany(Package::class, 'occasion_type_id');
    }
}
