<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['order_id', 'method', 'status', 'amount', 'bank_name', 'bank_account_number', 'bank_account_name', 'transfer_proof', 'qris_image_url', 'qris_reference', 'expired_at', 'paid_at', 'verified_by', 'verified_at', 'rejection_reason'];

    protected $casts = [
        'expired_at' => 'datetime',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
