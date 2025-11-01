<?php declare(strict_types=1);

namespace Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the enjoy_tw_media table.
 * This table handles public galleries linked to services (images/videos).
 */
return new class extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up(): void
  {
    Schema::create('enjoy_tw_media', function (Blueprint $table) {
      $table->id();
      $table->foreignId('service_id')->constrained('enjoy_tw_services')->onDelete('cascade');
      $table->enum('type', ['image', 'video']);
      $table->string('url');
      $table->string('caption')->nullable();
      $table->integer('order')->default(0);
      $table->timestamps();
      $table->index(['service_id', 'order']);
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down(): void
  {
    Schema::dropIfExists('enjoy_tw_media');
  }
};
