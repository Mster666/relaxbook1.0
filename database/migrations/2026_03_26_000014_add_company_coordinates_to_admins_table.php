<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            if (! Schema::hasColumn('admins', 'company_latitude')) {
                $table->decimal('company_latitude', 10, 7)->nullable()->after('company_address');
            }
            if (! Schema::hasColumn('admins', 'company_longitude')) {
                $table->decimal('company_longitude', 10, 7)->nullable()->after('company_latitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            if (Schema::hasColumn('admins', 'company_longitude')) {
                $table->dropColumn('company_longitude');
            }
            if (Schema::hasColumn('admins', 'company_latitude')) {
                $table->dropColumn('company_latitude');
            }
        });
    }
};
