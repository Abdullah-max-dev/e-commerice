<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_reports', function (Blueprint $table) {
            $table->boolean('vendor_warning_sent')->default(false)->after('vendor_justification');
            $table->timestamp('reporter_read_at')->nullable()->after('vendor_warning_sent');
        });

        Schema::table('vendor_notifications', function (Blueprint $table) {
            $table->boolean('is_archived')->default(false)->after('is_read');
            $table->timestamp('archived_at')->nullable()->after('is_archived');
            $table->timestamp('read_at')->nullable()->after('archived_at');
        });

        Schema::table('user_notifications', function (Blueprint $table) {
            $table->boolean('is_archived')->default(false)->after('is_read');
            $table->timestamp('archived_at')->nullable()->after('is_archived');
            $table->timestamp('read_at')->nullable()->after('archived_at');
        });
    }

    public function down(): void
    {
        Schema::table('user_notifications', function (Blueprint $table) {
            $table->dropColumn(['is_archived', 'archived_at', 'read_at']);
        });

        Schema::table('vendor_notifications', function (Blueprint $table) {
            $table->dropColumn(['is_archived', 'archived_at', 'read_at']);
        });

        Schema::table('product_reports', function (Blueprint $table) {
            $table->dropColumn(['vendor_warning_sent', 'reporter_read_at']);
        });
    }
};
