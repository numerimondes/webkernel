<?php declare(strict_types=1);

namespace Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the enjoy_tw_service_types table.
 * This table defines categories of services (e.g., nautical, wellness).
 */
return new class extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up(): void
  {
    Schema::create('enjoy_tw_service_types', function (Blueprint $table) {
      $table->id();
      $table->string('slug')->unique();
      $table->string('icon')->nullable();
      $table->boolean('is_active')->default(true);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down(): void
  {
    Schema::dropIfExists('enjoy_tw_service_types');
  }
};
