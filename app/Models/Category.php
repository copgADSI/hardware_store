<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = "categories";
    protected $fillable = ['category'];


    /* ACCESSORS AND MUTATORS */
    protected function category(): Attribute
    {
        return new Attribute(
            set: fn ($value) => strtolower($value),
            get: fn ($value) => ucwords($value),
        );
    }


    /* RELATIONSHIPS */

    public function product()
    {
        return $this->hasOne(Product::class);
    }
}
