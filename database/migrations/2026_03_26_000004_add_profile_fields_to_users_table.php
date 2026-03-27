<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('age');
            }
            if (! Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('birth_date');
            }
            if (! Schema::hasColumn('users', 'street_address')) {
                $table->string('street_address', 255)->nullable()->after('bio');
            }
            if (! Schema::hasColumn('users', 'city')) {
                $table->string('city', 120)->nullable()->after('street_address');
            }
            if (! Schema::hasColumn('users', 'state_province')) {
                $table->string('state_province', 120)->nullable()->after('city');
            }
            if (! Schema::hasColumn('users', 'zip_code')) {
                $table->string('zip_code', 24)->nullable()->after('state_province');
            }
            if (! Schema::hasColumn('users', 'country')) {
                $table->string('country', 120)->nullable()->after('zip_code');
            }
            if (! Schema::hasColumn('users', 'email_notifications')) {
                $table->boolean('email_notifications')->default(true)->after('country');
            }
            if (! Schema::hasColumn('users', 'sms_notifications')) {
                $table->boolean('sms_notifications')->default(false)->after('email_notifications');
            }
            if (! Schema::hasColumn('users', 'language')) {
                $table->string('language', 32)->nullable()->after('sms_notifications');
            }
            if (! Schema::hasColumn('users', 'timezone')) {
                $table->string('timezone', 64)->nullable()->after('language');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'birth_date',
                'bio',
                'street_address',
                'city',
                'state_province',
                'zip_code',
                'country',
                'email_notifications',
                'sms_notifications',
                'language',
                'timezone',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
