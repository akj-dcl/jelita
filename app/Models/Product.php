<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['category_id', 'image', 'video_path', 'name', 'slug', 'description', 'material', 'price', 'compare_at_price', 'sku', 'stock', 'weight', 'badge', 'rating', 'is_published', 'view_count'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    public function hasVariants(): bool
    {
        return $this->variants()->count() > 0;
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }
}
