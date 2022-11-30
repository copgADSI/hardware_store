<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $table = "cities";
    protected $fillable = ["city", "departament_id"];

    /* RELATIONSHIPS */
    public function departament()
    {
        return $this->hasOne(Departament::class, 'departament_id');
    }
}
