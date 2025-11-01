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
    Schema::create('local_licenses', function (Blueprint $table) {
      $table->id();
      $table->text('token_encrypted');
      $table->string('domain', 255);
      $table->timestamp('last_synced_at')->nullable();
      $table->timestamp('expires_at')->nullable();
      $table->enum('status', ['active', 'expired', 'revoked', 'pending'])->default('pending');
      $table->timestamps();
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
