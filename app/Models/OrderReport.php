<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'message',
        'status',
        'reviewed_at',
    ];
    protected $cast = [
        'reviewed_at' =>'database',
    ];
    public function order(){
        return $this->belongsTo(Order::class);
    }
    public function user(){
        return $this->belongTo(User::class);
    }
}
