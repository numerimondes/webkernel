<?php declare(strict_types=1);

namespace Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the enjoy_tw_providers table.
 * This table stores provider information, scoped privately.
 */
return new class extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up(): void
  {
    Schema::create('enjoy_tw_providers', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
      $table->string('company_name')->nullable();
      $table->string('phone')->nullable();
      $table->string('website')->nullable();
      $table->boolean('is_active')->default(true);
      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down(): void
  {
    Schema::dropIfExists('enjoy_tw_providers');
  }
};
