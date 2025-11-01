<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apt_website_themes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('apt_website_projects')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->index('idx_theme_slug');
            $table->text('description')->nullable();
            $table->json('theme_config')->nullable(); // Theme configuration JSON
            $table->boolean('is_active')->default(true)->index('idx_theme_active');
            $table->boolean('is_default')->default(false)->index('idx_theme_default');
            $table->string('version')->default('1.0.0');
            $table->string('css_file_path')->nullable(); // Path to generated CSS file
            $table->timestamp('generated_at')->nullable(); // When CSS was last generated
            $table->timestamps();

            $table->unique(['project_id', 'slug'], 'uq_proj_theme_slug');
            $table->index(['project_id', 'is_active'], 'idx_proj_theme_active');
            $table->index(['project_id', 'is_default'], 'idx_proj_theme_default');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apt_website_themes');
    }
};
