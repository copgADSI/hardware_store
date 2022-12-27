<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;
    const METHODS = [
        'CARD' => 1,
        'CASH' => 2
    ];
    protected $table = 'payment_methods';
    protected $fillable = [
        'method',
    ];

    public function payment()
    {
        return $this->hasMany(Payment::class);
    }
}
