<?php declare(strict_types=1);

namespace Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the enjoy_tw_service_type_translations table.
 * This table handles multilingual names and descriptions for service types.
 */
return new class extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up(): void
  {
    Schema::create('enjoy_tw_service_type_translations', function (Blueprint $table) {
      $table->id();
      $table->foreignId('service_type_id')->constrained('enjoy_tw_service_types')->onDelete('cascade');
      $table->string('language_code', 2);
      $table->string('name');
      $table->text('description')->nullable();
      $table->timestamps();
      $table->unique(['service_type_id', 'language_code'], 'unique_type_lang');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down(): void
  {
    Schema::dropIfExists('enjoy_tw_service_type_translations');
  }
};
