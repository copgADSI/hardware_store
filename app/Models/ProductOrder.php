<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOrder extends Model
{
    use HasFactory;
    protected $table = 'products_orders';
    protected $fillable = [
        'payment_id',
        'order_state_id',
        'product_id',
        'address_id'
    ];


    /* RELATIONSHIPS */

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    /* public function OrderState()
    {
        return $th
    } */
}
