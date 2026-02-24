<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products', 'p_id')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('users')->cascadeOnDelete();
            $table->string('reason', 80);
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'resolved', 'rejected'])->default('pending');
            $table->text('admin_note')->nullable();
            $table->text('vendor_justification')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_reports');
    }
};
