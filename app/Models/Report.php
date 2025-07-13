<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function branch()
{
    return $this->belongsTo(Branch::class);
}

public function manager()
{
    return $this->belongsTo(User::class, 'manager_id');
}

}
