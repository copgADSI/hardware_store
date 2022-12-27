<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $table = 'payments';
    protected $fillable = [
        'amount',
        'session_id',
        'user_id',
        'payment_method_id',
        'payment_state_id',
    ];


    /* RELATIONSHIPS */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payment_method()
    {

    }

    public function payment_state()
    {

    }
}
