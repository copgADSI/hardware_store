<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'images_carousel',
        'name',
        'price',
        'quantity',
        'description',
        'user_id',
    ];


    /* RELATIONSHIPS */
    public function user()
    {
        return $this->hasOne(User::class, 'user_id');
    }
}
