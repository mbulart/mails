<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('api_consumers', function (Blueprint $table): void {
            $table->string('smtp_mailer')->nullable()->after('last_used_at');
            $table->string('smtp_host')->nullable()->after('smtp_mailer');
            $table->unsignedInteger('smtp_port')->nullable()->after('smtp_host');
            $table->string('smtp_username')->nullable()->after('smtp_port');
            $table->text('smtp_password')->nullable()->after('smtp_username');
            $table->string('smtp_encryption')->nullable()->after('smtp_password');
            $table->string('smtp_from_address')->nullable()->after('smtp_encryption');
            $table->string('smtp_from_name')->nullable()->after('smtp_from_address');
        });
    }

    public function down(): void
    {
        Schema::table('api_consumers', function (Blueprint $table): void {
            $table->dropColumn([
                'smtp_mailer',
                'smtp_host',
                'smtp_port',
                'smtp_username',
                'smtp_password',
                'smtp_encryption',
                'smtp_from_address',
                'smtp_from_name',
            ]);
        });
    }
};
