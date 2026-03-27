<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            if (! Schema::hasColumn('admins', 'subscription_verified_at')) {
                $table->timestamp('subscription_verified_at')->nullable()->after('subscription_expires_at');
            }
        });

        if (Schema::hasColumn('admins', 'subscription_verified_at')) {
            DB::table('admins')
                ->whereNotNull('subscription_expires_at')
                ->whereNull('subscription_verified_at')
                ->update(['subscription_verified_at' => DB::raw('created_at')]);
        }
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            if (Schema::hasColumn('admins', 'subscription_verified_at')) {
                $table->dropColumn('subscription_verified_at');
            }
        });
    }
};
