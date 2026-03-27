<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            if (! Schema::hasColumn('rooms', 'code')) {
                $table->string('code', 32)->nullable()->after('name');
            }
            if (! Schema::hasColumn('rooms', 'capacity_min')) {
                $table->unsignedTinyInteger('capacity_min')->default(1)->after('code');
            }
            if (! Schema::hasColumn('rooms', 'capacity_max')) {
                $table->unsignedTinyInteger('capacity_max')->default(2)->after('capacity_min');
            }
            if (! Schema::hasColumn('rooms', 'price_per_hour')) {
                $table->decimal('price_per_hour', 10, 2)->default(0)->after('capacity_max');
            }
            if (! Schema::hasColumn('rooms', 'amenities')) {
                $table->json('amenities')->nullable()->after('price_per_hour');
            }
            if (! Schema::hasColumn('rooms', 'status')) {
                $table->string('status', 24)->default('available')->after('amenities');
            }
            if (! Schema::hasColumn('rooms', 'image')) {
                $table->string('image', 2048)->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $columns = ['code', 'capacity_min', 'capacity_max', 'price_per_hour', 'amenities', 'status', 'image'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('rooms', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

