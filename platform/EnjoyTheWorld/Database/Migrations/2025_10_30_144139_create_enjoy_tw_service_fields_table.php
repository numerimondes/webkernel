<?php declare(strict_types=1);

namespace Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the enjoy_tw_service_fields table.
 * This table defines dynamic fields per service type (e.g., duration, location).
 */
return new class extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up(): void
  {
    Schema::create('enjoy_tw_service_fields', function (Blueprint $table) {
      $table->id();
      $table->foreignId('service_type_id')->constrained('enjoy_tw_service_types')->onDelete('cascade');
      $table->string('field_key');
      $table->string('field_label');
      $table->enum('field_type', ['text', 'select', 'number', 'textarea']);
      $table->boolean('is_required')->default(false);
      $table->timestamps();
      $table->unique(['service_type_id', 'field_key'], 'unique_type_field');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down(): void
  {
    Schema::dropIfExists('enjoy_tw_service_fields');
  }
};
