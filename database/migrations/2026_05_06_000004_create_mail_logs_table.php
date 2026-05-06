<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('api_consumer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('mail_type_id')->constrained()->restrictOnDelete();
            $table->string('recipient');
            $table->string('subject');
            $table->json('payload');
            $table->string('status')->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['mail_type_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_logs');
    }
};
