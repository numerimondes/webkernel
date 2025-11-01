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
    Schema::create('modules', function (Blueprint $table) {
      $table->id();

      // Module identification
      $table->string('identifier', 100)->index(); // e.g., 'crm-pro'
      $table->string('name', 255); // Display name
      $table->string('version', 20); // SemVer (e.g., '1.2.3')
      $table->text('description')->nullable();

      // File information
      $table->string('zip_path', 500); // Storage path
      $table->string('hash', 64); // SHA256 for integrity
      $table->bigInteger('file_size'); // Bytes

      // Metadata (JSON for changelog, dependencies, etc.)
      $table->json('metadata')->nullable();

      // Status
      $table
        ->enum('status', ['active', 'archived', 'deprecated'])
        ->default('active')
        ->index();

      // Organization (for custom modules)
      $table->foreignId('organization_id')->nullable()->constrained()->cascadeOnDelete();

      $table->timestamps();
      $table->softDeletes();

      // Unique constraint: one version per identifier
      $table->unique(['identifier', 'version']);

      // Index for queries
      $table->index(['identifier', 'status']);
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
