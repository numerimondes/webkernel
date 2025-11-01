<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations
   *
   * @return void
   */
  public function up(): void
  {
    Schema::create('organizations', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('slug')->unique();
      $table->string('avatar_url')->nullable();
      $table->json('settings')->nullable();
      $table->boolean('is_active')->default(true);
      $table->timestamps();
      $table->softDeletes();

      $table->index('slug');
      $table->index('is_active');
    });

    Schema::create('organization_user', function (Blueprint $table) {
      $table->id();
      $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
      $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
      $table->string('role')->default('member');
      $table->json('permissions')->nullable();
      $table->timestamps();

      $table->unique(['organization_id', 'user_id']);
    });

    Schema::create('softwares', function (Blueprint $table) {
      $table->id();
      $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
      $table->string('name');
      $table->string('slug')->unique();
      $table->string('namespace')->unique();
      $table->boolean('is_active')->default(true);
      $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
      $table->foreignId('updated_by')->constrained('users')->cascadeOnDelete();
      $table->timestamps();

      $table->index('organization_id');
      $table->index('slug');
    });

    Schema::create('software_cores', function (Blueprint $table) {
      $table->id();
      $table->foreignId('software_id')->constrained('softwares')->onDelete('cascade');
      $table->string('name');
      $table->string('version');
      $table->json('zip_path');
      $table->string('install_path');
      $table->string('namespace');
      $table->string('hash');
      $table->string('validation_status')->nullable();
      $table->json('metadata')->nullable();
      $table->timestamps();
      $table->index('software_id');
    });
  }

  /**
   * Reverse the migrations
   *
   * @return void
   */
  public function down(): void
  {
    Schema::dropIfExists('software_cores');
    Schema::dropIfExists('softwares');
    Schema::dropIfExists('organization_user');
    Schema::dropIfExists('organizations');
  }
};
