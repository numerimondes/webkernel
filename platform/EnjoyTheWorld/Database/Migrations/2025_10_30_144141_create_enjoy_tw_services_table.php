<?php declare(strict_types=1);

namespace Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the enjoy_tw_services table.
 * This table stores service offers, without exposing providers publicly.
 */
return new class extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up(): void
  {
    Schema::create('enjoy_tw_services', function (Blueprint $table) {
      $table->id();
      $table->foreignId('provider_id')->constrained('enjoy_tw_providers')->onDelete('cascade');
      $table->foreignId('service_type_id')->constrained('enjoy_tw_service_types')->onDelete('cascade');
      $table->decimal('price', 8, 2);
      $table->string('duration')->nullable();
      $table->string('location')->nullable();
      $table->boolean('is_active')->default(true);
      $table->boolean('is_featured')->default(false);
      $table->timestamps();
      $table->softDeletes();
      $table->index(['provider_id', 'service_type_id']);
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down(): void
  {
    Schema::dropIfExists('enjoy_tw_services');
  }
};
