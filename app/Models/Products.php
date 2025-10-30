<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
    ];      

    public function cartItems(){
        return $this->hasMany(CartItems::class);
    }

    public function orderItems(){
        return $this->hasMany(OrderItems::class);
    }
}
