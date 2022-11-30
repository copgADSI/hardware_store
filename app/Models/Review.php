<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $table = "reviews";
    protected $fillable = ['comments', 'rating', 'user_id', 'product_id'];

    protected function comments(): Attribute
    {
        return new Attribute(
            set: fn ($value) => strtolower($value)
        );
    }

    /* RELATIONSHIPS */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsToMany(Product::class, 'product_id');
    }
}
