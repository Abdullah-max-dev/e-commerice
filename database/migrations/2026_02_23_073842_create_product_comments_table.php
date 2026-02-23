<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('p_id')->on('products')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('product_comments')->cascadeOnDelete();
            $table->text('comment');
            $table->unsignedTinyInteger('rating')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_comments');
    }
};
