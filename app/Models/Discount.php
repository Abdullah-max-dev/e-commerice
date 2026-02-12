<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable=[
        'v_id',
        'p_id',
        'type',
        'value',
        'discount_price',
        'is_active',
        'starts_at',
        'ends_at'
    ];
    public function product()
    {
        return $this->belongsTo(Product::class, 'p_id', 'p_id');
    }
    protected static function booted()
    {
        static::created(function ($discount) {
            $discount->product()->update([
                'is_top_deal' => 1
            ]);
        });

        static::deleted(function ($discount) {
            $discount->product()->update([
                'is_top_deal' => 0
            ]);
        });
    }


}

