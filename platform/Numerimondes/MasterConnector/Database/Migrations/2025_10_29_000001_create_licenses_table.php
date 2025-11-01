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
    Schema::create('licenses', function (Blueprint $table) {
      $table->id();

      // Token hash (SHA256, never store plaintext)
      $table->string('token_hash', 64)->unique()->index();

      // Domain validation
      $table->string('domain', 255)->index();

      // Status
      $table
        ->enum('status', ['active', 'expired', 'revoked'])
        ->default('active')
        ->index();

      // Expiration (nullable for perpetual licenses)
      $table->timestamp('expires_at')->nullable()->index();

      // Metadata (JSON for client info, plan details, etc.)
      $table->json('metadata')->nullable();

      // Audit fields
      $table->timestamp('last_validated_at')->nullable();
      $table->string('last_validated_ip', 45)->nullable();

      // Organization (optional PROPLUS feature)
      $table->foreignId('organization_id')->nullable()->constrained()->cascadeOnDelete();

      $table->timestamps();
      $table->softDeletes();

      // Composite index for validation queries
      $table->index(['token_hash', 'domain', 'status']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    // Forward-only: no down() logic per spec
    throw new RuntimeException('Migration rollback not supported. Use backups instead.');
  }
};
