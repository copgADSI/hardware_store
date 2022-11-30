<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departament extends Model
{
    use HasFactory;
    protected $table = "departaments";
    protected $fillable = ['departament'];

    /* RELATIONSHIPS */
    public function cities()
    {
        return $this->belongsToMany(City::class);
    }
}
