<?php declare(strict_types=1);

namespace Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the enjoy_tw_service_translations table.
 * This table manages multilingual titles and descriptions for services.
 */
return new class extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up(): void
  {
    Schema::create('enjoy_tw_service_translations', function (Blueprint $table) {
      $table->id();
      $table->foreignId('service_id')->constrained('enjoy_tw_services')->onDelete('cascade');
      $table->string('language_code', 2);
      $table->string('title');
      $table->text('description');
      $table->timestamps();
      $table->unique(['service_id', 'language_code'], 'unique_service_lang');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down(): void
  {
    Schema::dropIfExists('enjoy_tw_service_translations');
  }
};
