<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    protected $table = "addresses";
    protected $fillable = [
        'line_one',
        'line_two',
        'phone',
        'user_id',
        'city_id',
        'departament_id'
    ];

    /* RELATIONSHIP */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
