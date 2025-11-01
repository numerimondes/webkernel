<?php declare(strict_types=1);

namespace Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the enjoy_tw_service_field_values table.
 * This table stores specific field values for each service based on its type.
 */
return new class extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up(): void
  {
    Schema::create('enjoy_tw_service_field_values', function (Blueprint $table) {
      $table->id();
      $table->foreignId('service_id')->constrained('enjoy_tw_services')->onDelete('cascade');
      $table->string('field_key');
      $table->text('value')->nullable();
      $table->timestamps();
      $table->unique(['service_id', 'field_key'], 'unique_service_field');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down(): void
  {
    Schema::dropIfExists('enjoy_tw_service_field_values');
  }
};
