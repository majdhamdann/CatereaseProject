<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItemServiceMap extends Model
{
    use HasFactory;
     protected $guarded = ['id'];

    public function branchServiceType()
{
    return $this->belongsTo(BranchServiceType::class);
}

}
