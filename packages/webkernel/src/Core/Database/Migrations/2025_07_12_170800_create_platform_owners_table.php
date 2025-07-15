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
        Schema::create('rbac_platform_owners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('panel_id');
            $table->boolean('is_eternal_owner')->default(false);
            $table->timestamp('when')->nullable();
            $table->timestamp('until')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'panel_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rbac_platform_owners');
    }
}; 