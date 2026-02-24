<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorNotification extends Model
{
    protected $fillable = [
        'vendor_id',
        'type',
        'title',
        'message',
        'is_read',
        'meta',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'meta' => 'array',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
}
