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
        'is_archived',
        'archived_at',
        'read_at',
        'meta',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_archived' => 'boolean',
        'archived_at' => 'datetime',
        'read_at' => 'datetime',
        'meta' => 'array',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
}
