<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('booking_therapist_segments')) {
            return;
        }

        Schema::create('booking_therapist_segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('therapist_id')->constrained('therapists')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->cascadeOnUpdate()->nullOnDelete();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->timestamps();

            $table->index(['therapist_id', 'starts_at']);
            $table->index(['therapist_id', 'ends_at']);
            $table->index(['booking_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_therapist_segments');
    }
};

