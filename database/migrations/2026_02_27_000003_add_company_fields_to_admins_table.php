<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            if (! Schema::hasColumn('admins', 'company_name')) {
                $table->string('company_name', 191)->nullable()->after('profile_picture');
            }
            if (! Schema::hasColumn('admins', 'company_logo')) {
                $table->string('company_logo', 2048)->nullable()->after('company_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            if (Schema::hasColumn('admins', 'company_logo')) {
                $table->dropColumn('company_logo');
            }
            if (Schema::hasColumn('admins', 'company_name')) {
                $table->dropColumn('company_name');
            }
        });
    }
};
