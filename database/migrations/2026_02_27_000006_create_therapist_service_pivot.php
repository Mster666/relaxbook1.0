<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('service_therapist')) {
            return;
        }

        Schema::create('service_therapist', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('therapist_id');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->index(['service_id', 'therapist_id']);
            $table->timestamps();
            $table->unique(['service_id', 'therapist_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_therapist');
    }
};
