<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('subscription_logs')) {
            return;
        }

        Schema::table('subscription_logs', function (Blueprint $table) {
            if (Schema::hasColumn('subscription_logs', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('subscription_logs')) {
            return;
        }

        Schema::table('subscription_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('subscription_logs', 'payment_method')) {
                $table->string('payment_method', 64)->nullable()->after('paid_at');
            }
        });
    }
};
