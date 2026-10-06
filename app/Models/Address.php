<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id', 'label', 'recipient_name', 'phone',
        'address_line', 'kelurahan', 'city', 'province', 'postal_code',
        'destination_id', 'destination_label', 'is_default',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
