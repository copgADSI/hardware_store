<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;
    protected $table = "favorites";
    protected $fillable = ['user_id', 'product_id'];

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
