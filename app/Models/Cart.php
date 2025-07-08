<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CartItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Cart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart query()
 * @mixin \Eloquent
 */
class Cart extends Model
{
    use HasFactory;
     protected $guarded = ['id'];
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function items() {
        return $this->hasMany(CartItem::class, 'cart_id');
    }
}
