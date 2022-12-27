<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderState extends Model
{
    use HasFactory;
    protected $table = 'order_states';
    protected $fillable = ['state'];
    const STATES = [
        'PROCESSED' => 1,

    ];
    /* RELATIONSHIPS */
    public function Orders()
    {
        return $this->hasMany(ProductOrder::class);
    }
}
