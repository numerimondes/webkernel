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
    Schema::create('license_modules', function (Blueprint $table) {
      $table->id();

      // Relationships
      $table->foreignId('license_id')->constrained()->cascadeOnDelete();
      $table->foreignId('module_id')->constrained()->cascadeOnDelete();

      // Timestamps
      $table->timestamp('granted_at')->useCurrent();
      $table->timestamp('revoked_at')->nullable();

      $table->timestamps();

      // Unique constraint: one assignment per license-module pair
      $table->unique(['license_id', 'module_id']);

      // Index for queries
      $table->index(['license_id', 'revoked_at']);
      $table->index('module_id');
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
