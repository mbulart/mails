<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('api_consumers', function (Blueprint $table): void {
            if (! Schema::hasColumn('api_consumers', 'api_key_plain')) {
                $table->text('api_key_plain')->nullable()->after('api_key_preview');
            }
        });
    }

    public function down(): void
    {
        Schema::table('api_consumers', function (Blueprint $table): void {
            if (Schema::hasColumn('api_consumers', 'api_key_plain')) {
                $table->dropColumn('api_key_plain');
            }
        });
    }
};
