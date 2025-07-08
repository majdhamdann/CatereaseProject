<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
     protected $guarded = ['id'];
     public function bill() {
        return $this->belongsTo(Bill::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
