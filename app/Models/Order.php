<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'order_number', 'status', 'subtotal', 'shipping_cost', 'tax', 'discount', 'total', 'shipping_method', 'notes', 'ship_name', 'ship_phone', 'ship_address', 'ship_city', 'ship_province', 'ship_postal_code', 'tracking_number'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
