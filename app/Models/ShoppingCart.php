<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model
{
    use HasFactory;
    protected $table = "shopping_carts";
    protected $fillable = [
        'product_id',
        'user_id',
        'quantity',
        'subtotal'
    ];

    /* RELATIONSHIPS */
    public function user()
    {
        return $this->belongsToMany(User::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsToMany(Product::class, 'product_id');
    }
}
