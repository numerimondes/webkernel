<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('download_logs', function (Blueprint $table) {
      $table->id();

      // Relationships
      $table->foreignId('license_id')->nullable()->constrained()->nullOnDelete();
      $table->foreignId('module_id')->nullable()->constrained()->nullOnDelete();

      // Request information
      $table->string('ip_address', 45)->index();
      $table->string('user_agent', 500)->nullable();

      // Result
      $table->boolean('success')->default(false)->index();
      $table->text('error_message')->nullable();

      // Metadata (JSON for request details, response time, etc.)
      $table->json('metadata')->nullable();

      // Timestamp
      $table->timestamp('downloaded_at')->useCurrent()->index();

      // Composite index for abuse detection queries
      $table->index(['license_id', 'downloaded_at']);
      $table->index(['ip_address', 'downloaded_at']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    throw new RuntimeException('Migration rollback not supported. Use backups instead.');
  }
};
