<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['rooms', 'services', 'therapists', 'holidays', 'bookings'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                if (! Schema::hasColumn($table->getTable(), 'admin_id')) {
                    $table->foreignId('admin_id')->nullable()->constrained('admins')->cascadeOnUpdate()->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['rooms', 'services', 'therapists', 'holidays', 'bookings'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'admin_id')) {
                    $table->dropConstrainedForeignId('admin_id');
                }
            });
        }
    }
};
