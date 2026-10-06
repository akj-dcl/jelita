<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'name', 'sku', 'price', 'stock', 'weight', 'options', 'is_active', 'sort_order'];

    protected $casts = [
        'options' => 'array',
        'price' => 'decimal:2',
        'weight' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
