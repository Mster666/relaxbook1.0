<?php

use App\Models\Admin;
use App\Models\SubscriptionLog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('admins') || ! Schema::hasTable('subscription_logs')) {
            return;
        }

        $exists = Admin::query()
            ->whereNotNull('subscription_expires_at')
            ->exists();

        if (! $exists) {
            return;
        }

        $adminIdsWithLogs = SubscriptionLog::query()
            ->select('admin_id')
            ->distinct()
            ->pluck('admin_id')
            ->all();

        $adminIdsWithLogs = array_map('intval', $adminIdsWithLogs);

        Admin::query()
            ->whereNotNull('subscription_expires_at')
            ->when($adminIdsWithLogs !== [], fn ($q) => $q->whereNotIn('id', $adminIdsWithLogs))
            ->orderBy('id')
            ->chunk(200, function ($admins): void {
                foreach ($admins as $admin) {
                    $startsAt = ($admin->subscription_verified_at ?? $admin->created_at ?? now())->toDateString();
                    $endsAt = $admin->subscription_expires_at->toDateString();
                    $isExpired = now()->startOfDay()->gt($admin->subscription_expires_at->copy()->startOfDay());

                    SubscriptionLog::query()->create([
                        'admin_id' => $admin->id,
                        'business_name' => (string) ($admin->company_name ?: $admin->name),
                        'subscription_plan' => '₱24,999/month',
                        'amount' => 24999,
                        'starts_at' => $startsAt,
                        'ends_at' => $endsAt,
                        'payment_status' => $isExpired ? 'EXPIRED' : 'PAID',
                        'paid_at' => $isExpired ? null : ($admin->subscription_verified_at ?? $admin->created_at ?? now()),
                        'created_at' => $admin->subscription_verified_at ?? $admin->created_at ?? now(),
                        'updated_at' => $admin->subscription_verified_at ?? $admin->created_at ?? now(),
                    ]);
                }
            });
    }

    public function down(): void
    {
        if (! Schema::hasTable('subscription_logs')) {
            return;
        }

        DB::table('subscription_logs')
            ->where('subscription_plan', '₱24,999/month')
            ->where('amount', 24999)
            ->delete();
    }
};
