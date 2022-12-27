<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSate extends Model
{
    use HasFactory;
    const STATES = [
        'UNPAID' => 1,
        'PAID' => 2,
        'SUCCEEDED' => 3,
        'PAYMENT_CANCELLED' => 4,
    ];
    protected $table = 'payments_states';
    protected $fillable = [
        'state'
    ];


    /* MUTATIONS - ACCESSORS */
    protected function state(): Attribute
    {
        return new Attribute(
            set: fn ($value) => strtolower($value),
            get: fn ($value) => strtoupper($value)
        );
    }

    /* RELATIONSHIPS */
    public function payments()
    {
        return $this->belongsTo(Payment::class, 'payment_state_id');
    }
}
