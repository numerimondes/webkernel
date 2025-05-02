<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('render_hook_settings', function (Blueprint $table) {
            $table->id();
            $table->string('hook_key')->unique();
            $table->string('icon')->nullable();
            $table->string('where_placed')->nullable();
            $table->text('scopes')->nullable();
            $table->string('translation_desc_key');
            $table->string('view_path');
            $table->boolean('enabled')->default(1);
            $table->boolean('customizable')->default(0);
            $table->text('customization_rel_ink')->nullable();
            $table->timestamps();

            $table->index('hook_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('render_hook_settings');
    }
};
