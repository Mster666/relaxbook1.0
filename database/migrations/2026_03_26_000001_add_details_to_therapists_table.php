<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('therapists', function (Blueprint $table) {
            if (! Schema::hasColumn('therapists', 'title')) {
                $table->string('title', 191)->nullable()->after('name');
            }
            if (! Schema::hasColumn('therapists', 'gender')) {
                $table->string('gender', 24)->nullable()->after('phone');
            }
            if (! Schema::hasColumn('therapists', 'languages')) {
                $table->json('languages')->nullable()->after('gender');
            }
            if (! Schema::hasColumn('therapists', 'certifications')) {
                $table->json('certifications')->nullable()->after('languages');
            }
        });
    }

    public function down(): void
    {
        Schema::table('therapists', function (Blueprint $table) {
            $columns = ['title', 'gender', 'languages', 'certifications'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('therapists', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

