<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'status',
        'client_id',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
 
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
 
    public function products()
    {
        return $this->hasManyThrough(Product::class, OrderItem::class, 'order_id', 'id', 'id', 'product_id');
    }
}