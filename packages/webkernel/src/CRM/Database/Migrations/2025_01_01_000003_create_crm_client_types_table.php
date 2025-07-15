<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_client_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('model_type')->comment('Type de modèle (App\Models\User, etc.)');
            $table->string('module')->nullable()->comment('Module propriétaire');
            $table->boolean('is_active')->default(true);
            $table->json('capabilities')->nullable()->comment('Capacités client disponibles');
            $table->timestamps();
            
            $table->index(['model_type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_client_types');
    }
}; 