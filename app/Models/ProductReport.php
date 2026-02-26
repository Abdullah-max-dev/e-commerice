<?php

namespace App\Models;

 use Illuminate\Database\Eloquent\Factories\HasFactory;
 use Illuminate\Database\Eloquent\Model;

 class ProductReport extends Model
 {
     use HasFactory;

     public const REASONS = [
         'Fake Product',
         'Wrong Description',
         'Copyright Issue',
         'Offensive Content',
         'Scam / Fraud',
         'Other',
     ];

     protected $fillable = [
         'product_id',
         'user_id',
         'vendor_id',
         'reason',
         'message',
         'status',
         'admin_note',
         'vendor_justification',
+        'vendor_warning_sent',
+        'reporter_read_at',
         'resolved_at',
     ];

     protected $casts = [
+        'vendor_warning_sent' => 'boolean',
+        'reporter_read_at' => 'datetime',
         'resolved_at' => 'datetime',
     ];

     public function product()
     {
         return $this->belongsTo(Product::class, 'product_id', 'p_id');
     }

     public function user()
     {
         return $this->belongsTo(User::class);
     }

     public function vendor()
     {
         return $this->belongsTo(User::class, 'vendor_id');
     }
 }
