<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('admin_operating_breaks')) {
            return;
        }

        Schema::create('admin_operating_breaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operating_hour_id')->constrained('admin_operating_hours')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('label', 120)->nullable();
            $table->time('starts_at');
            $table->time('ends_at');
            $table->timestamps();

            $table->index(['operating_hour_id', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_operating_breaks');
    }
};

