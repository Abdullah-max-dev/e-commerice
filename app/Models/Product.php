<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CartItem;
use App\Models\OrderItem;

class Product extends Model
{

    protected $primaryKey = 'p_id';
     public $timestamps = false;
    protected $fillable = [
        'v_id',
        'p_name',
        'p_price',
        'is_top_deal',
        'c_id',
        'p_stock',
        'p_description',
        'p_image',
        'final_price'

    ];
    protected $casts = [
        'final_price' => 'decimal:2',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class, 'c_id', 'c_id');
    }
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'p_id', 'p_id');
    }

    public function mainImage()
    {
        return $this->hasOne(ProductImage::class, 'p_id', 'p_id')
                    ->where('is_main', 1);
    }
    public function discount(){
        return $this->hasOne(Discount::class, 'p_id', 'p_id')
                    ->where('is_active', 1)
                    ->where(function ($query) {
                        $query->whereNull('starts_at')
                              ->orWhereDate('starts_at', '<=', now());
                    })
                    ->where(function ($query) {
                        $query->whereNull('ends_at')
                              ->orWhereDate('ends_at', '>=', now());
                    });
    }
    public function getFinalPriceAttribute()
    {
        $price = $this->p_price;

        if ($this->discount && $this->discount->is_active) {

            if ($this->discount->type === 'percentage') {
                return $price - ($price * $this->discount->value / 100);
            }

            if ($this->discount->type === 'fixed') {
                return max($price - $this->discount->value, 0);
            }
        }

        return $price;
    }
    public function vender()
    {
        return $this->belongsTo(User::class, 'v_id');
    }
    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'p_id', 'p_id');
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'p_id', 'p_id');
    }

}
