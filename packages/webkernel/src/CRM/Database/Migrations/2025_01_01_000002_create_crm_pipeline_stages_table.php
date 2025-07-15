<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_pipeline_stages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pipeline_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->decimal('win_probability', 5, 2)->default(0)->comment('Probabilité de succès en %');
            $table->string('color')->nullable()->comment('Couleur pour l\'affichage');
            $table->boolean('is_client_stage')->default(false)->comment('Étape où le prospect devient client');
            $table->json('settings')->nullable()->comment('Configuration spécifique à l\'étape');
            $table->timestamps();
            
            $table->foreign('pipeline_id')->references('id')->on('crm_pipelines')->onDelete('cascade');
            $table->index(['pipeline_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_pipeline_stages');
    }
}; 