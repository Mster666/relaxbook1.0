<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('booking_service_therapist')) {
            return;
        }

        Schema::create('booking_service_therapist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('therapist_id')->nullable()->constrained('therapists')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();

            $table->unique(['booking_id', 'service_id'], 'booking_service_unique');
            $table->index(['booking_id', 'therapist_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_service_therapist');
    }
};

