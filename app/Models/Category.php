<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $primaryKey = 'c_id';
    protected $fillable=['c_name','c_commission','is_popular'];
     public function products()
    {
        return $this->hasMany(Product::class, 'c_id', 'c_id');
    }
}
