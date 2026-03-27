<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('admin_settings')) {
            return;
        }

        Schema::table('admin_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('admin_settings', 'email_notifications')) {
                $table->boolean('email_notifications')->default(true)->after('timezone');
            }
            if (! Schema::hasColumn('admin_settings', 'sms_notifications')) {
                $table->boolean('sms_notifications')->default(false)->after('email_notifications');
            }
            if (! Schema::hasColumn('admin_settings', 'language')) {
                $table->string('language', 32)->nullable()->after('sms_notifications');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('admin_settings')) {
            return;
        }

        Schema::table('admin_settings', function (Blueprint $table) {
            if (Schema::hasColumn('admin_settings', 'language')) {
                $table->dropColumn('language');
            }
            if (Schema::hasColumn('admin_settings', 'sms_notifications')) {
                $table->dropColumn('sms_notifications');
            }
            if (Schema::hasColumn('admin_settings', 'email_notifications')) {
                $table->dropColumn('email_notifications');
            }
        });
    }
};

