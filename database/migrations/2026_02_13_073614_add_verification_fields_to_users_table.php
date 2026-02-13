<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('verification_status')->default('unverified')->after('role');
            $table->json('verification_data')->nullable()->after('verification_status');
            $table->text('verification_note')->nullable()->after('verification_data');
            $table->timestamp('verification_submitted_at')->nullable()->after('verification_note');
            $table->timestamp('verification_reviewed_at')->nullable()->after('verification_submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'verification_status',
                'verification_data',
                'verification_note',
                'verification_submitted_at',
                'verification_reviewed_at',
            ]);
        });
    }
};
