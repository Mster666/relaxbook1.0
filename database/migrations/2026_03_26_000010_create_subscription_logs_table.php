<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('subscription_logs')) {
            return;
        }

        Schema::create('subscription_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('admins')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('business_name', 191);
            $table->string('subscription_plan', 64)->default('₱24,999/month');
            $table->unsignedInteger('amount')->default(24999);
            $table->date('starts_at');
            $table->date('ends_at');
            $table->string('payment_status', 16)->default('PAID');
            $table->dateTime('paid_at')->nullable();
            $table->string('payment_method', 64)->nullable();
            $table->timestamps();

            $table->index(['admin_id', 'starts_at']);
            $table->index(['payment_status', 'paid_at']);
            $table->index(['ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_logs');
    }
};
