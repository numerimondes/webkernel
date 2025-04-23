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
        Schema::table('users', function (Blueprint $table) {
            // Ajout de nouvelles colonnes
            $table->string('username')->unique();
            $table->string('mobile')->nullable()->unique();
            $table->string('whatsapp')->nullable()->unique();
            $table->string('timezone')->nullable();
            $table->string('user_lang', 2)->default('en'); // Définir la valeur par défaut

            // Activité de l'utilisateur
            $table->boolean('forceChangePassword')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_banned')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            // subscriptions
            $table->boolean('marketing_callable')->default(true);
            $table->boolean('marketing_whatsappable')->default(true);
            $table->boolean('marketing_smsable')->default(true);

            // Multi-tenant
            $table->integer('belongs_to')->default(1); // Tenant before implementation
        });

        Schema::create('history_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->integer('last_activity')->index();
            $table->timestamp('archived_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Suppression des colonnes ajoutées
            $table->dropColumn('username');
            $table->dropColumn('mobile');
            $table->dropColumn('whatsapp');
            $table->dropColumn('timezone');
            $table->dropColumn('user_lang');
            $table->dropColumn('forceChangePassword');
            $table->dropColumn('is_active');
            $table->dropColumn('is_banned');
            $table->dropColumn('created_by');
            $table->dropColumn('marketing_callable');
            $table->dropColumn('marketing_whatsappable');
            $table->dropColumn('marketing_smsable');
            $table->dropColumn('belongs_to');
        });

        // Suppression de la table history_sessions
        Schema::dropIfExists('history_sessions');
    }
};
