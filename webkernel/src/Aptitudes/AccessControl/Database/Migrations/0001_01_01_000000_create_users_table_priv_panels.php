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
    Schema::create('users_priv_panel_access', function (Blueprint $table): void {
      $table->id();
      $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
      $table->string('panel_id', 50);
      $table->boolean('is_active')->default(true);
      $table->timestamps();

      $table->unique(['user_id', 'panel_id']);
      $table->index(['user_id', 'is_active']);
      $table->index('panel_id');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('users_priv_panel_access');
  }
};
