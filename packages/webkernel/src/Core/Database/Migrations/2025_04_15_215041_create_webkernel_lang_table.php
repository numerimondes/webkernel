<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('webkernel_lang', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // ex: 'en', 'fr'
            $table->string('ISO')->unique(); // ex: 'en', 'fr'
            $table->string('label');          // ex: 'English', 'Français'
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->integer('tenant_id')->default(1); // Tenant before implementation

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webkernel_lang');
    }
};
