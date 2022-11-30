<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = "products";
    protected $fillable = [
        'carousel',
        'name',
        'price',
        'quantity',
        'description',
        'user_id',
        'category_id',
        'brand_id'
    ];

    protected function carousel(): Attribute
    {
        return new Attribute(
            get: function ($value) {
                return explode(',', $value);
            }
        );
    }

    protected function name(): Attribute
    {
        return new Attribute(
            get: fn ($value) => strtoupper($value)
        );
    }

    /* protected function price(): Attribute
    {
        return new Attribute(
            get: fn ($value) => number_format($value, ',')
        );
    } */
    /* RELATIONSHIPS */
    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'product_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function shoppingCart()
    {
        return $this->hasMany(ShoppingCart::class, 'product_id');
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id');
    }
}
