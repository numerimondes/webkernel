<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('login_at')->nullable();
            $table->timestamp('logout_at')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->index('session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_history');
    }
};
