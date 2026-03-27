<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->boolean('is_super_admin')->default(false)->after('email_verified_at');
            $table->boolean('is_active')->default(true)->after('is_super_admin');
            $table->timestamp('subscription_expires_at')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['is_super_admin', 'is_active', 'subscription_expires_at']);
        });
    }
};

