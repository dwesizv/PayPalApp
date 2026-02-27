<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    
    const STATUS_CREATED   = 'created';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELED  = 'canceled';
    const STATUS_FAILED    = 'failed';

    protected $table = 'order';

    protected $fillable = [
        'address',
        'amount',
        'capture_id',
        'cart',
        'currency',
        'email',
        'name',
        'paypal_order_id',
        'status',
    ];

    protected $casts = [
        'cart' => 'array',
        'amount' => 'decimal:2',
    ];

    static function rules($id = null) {
        return [
            'name'            => 'required|string|max:255',
            'address'         => 'required|string|max:255',
            'email'           => 'required|email|max:255',
            'cart'            => 'nullable|array',
            'paypal_order_id' => 'required|string|max:64|unique:orders,paypal_order_id,' . $id,
            'capture_id'      => 'nullable|string|max:64',
            'amount'          => 'required|numeric|min:0',
            'currency'        => 'required|string|size:3',
            'status'          => 'in:created,completed,canceled,failed',
        ];
    }
}