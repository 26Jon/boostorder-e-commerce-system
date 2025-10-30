<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carts extends Model
{
    protected $fillable = [
        'user_id',
        'total_price',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function cartItems(){
        return $this->hasMany(CartItems::class, 'cart_id');
    }
}
