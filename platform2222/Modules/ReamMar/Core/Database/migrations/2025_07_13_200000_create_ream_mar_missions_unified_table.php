<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        /**
         * Données client associées à une mission
         */
        Schema::create('ream_mar_clients', function (Blueprint $table) {
            $table->id();
            $table->string('civility')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->text('address')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->text('notes')->nullable();
            $table->string('avatar')->nullable();
            $table->timestamps();
        });

        /**
         * Informations sur le bien à rénover
         */
        Schema::create('ream_mar_properties', function (Blueprint $table) {
            $table->id();
            $table->text('address')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('type')->nullable()->comment('Maison, Appartement, etc.');
            $table->string('usage')->nullable()->comment('Usage principal, secondaire, locatif...');
            $table->string('household_status')->nullable()->comment('Propriétaire, locataire, etc.');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        /**
         * Audit énergétique ou technique
         */
        Schema::create('ream_mar_audits', function (Blueprint $table) {
            $table->id();
            $table->boolean('required')->default(false)->comment('Audit requis ou non');
            $table->date('date')->nullable();
            $table->string('type')->nullable();
            $table->string('report_path')->nullable()->comment('Chemin vers le rapport d\'audit');
            $table->text('notes')->nullable();
            $table->decimal('fees', 10, 2)->nullable()->comment('Frais de l\'audit');
            $table->timestamps();
        });

        /**
         * Financement du projet
         */
        Schema::create('ream_mar_financings', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2)->nullable()->comment('Montant total du financement');
            $table->decimal('aids', 10, 2)->nullable()->comment('Montant des aides perçues');
            $table->decimal('loan', 10, 2)->nullable()->comment('Montant total des prêts');
            $table->text('notes')->nullable();

            $table->decimal('ecoptz_amount', 10, 2)->nullable();
            $table->decimal('ecoptz_rate', 6, 4)->nullable();
            $table->integer('ecoptz_duration_months')->nullable();

            $table->decimal('bank_loan_amount', 10, 2)->nullable();
            $table->decimal('bank_loan_rate', 6, 4)->nullable();
            $table->integer('bank_loan_duration_months')->nullable();

            $table->timestamps();
        });

        /**
         * Informations sur les travaux réalisés
         */
        Schema::create('ream_mar_works', function (Blueprint $table) {
            $table->id();
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2)->nullable()->comment('Montant total des travaux');
            $table->string('status')->nullable()->comment('Statut : à faire, en cours, terminé...');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('company')->nullable();
            $table->string('company_siret')->nullable();
            $table->timestamps();
        });

        /**
         * Clôture de la mission
         */
        Schema::create('ream_mar_completions', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->nullable()->comment('Statut de finalisation : done, pending, etc.');
            $table->timestamps();
        });

        /**
         * Table principale représentant une mission MAR
         * Créée à la fin car elle contient les foreign keys vers toutes les autres tables
         */
        Schema::create('ream_mar_missions', function (Blueprint $table) {
            $table->id();

            // CRM Integration
            $table->unsignedBigInteger('pipeline_id')->nullable();
            $table->unsignedBigInteger('pipeline_stage_id')->nullable();
            $table->string('deal_status')->default('active')->comment('active, won, lost, cancelled');
            $table->decimal('deal_value', 12, 2)->nullable()->comment('Valeur du deal');
            $table->date('expected_close_date')->nullable()->comment('Date de clôture prévue');
            $table->date('actual_close_date')->nullable()->comment('Date de clôture réelle');

            // Prospect/Contact Information
            $table->string('prospect_type')->nullable()->comment('Type de prospect');
            $table->string('prospect_source')->nullable()->comment('Source du prospect');
            $table->date('contact_date')->nullable()->comment('Date de premier contact');
            $table->string('contact_method')->nullable()->comment('Méthode de contact');
            $table->text('prospect_notes')->nullable();
            $table->text('contact_notes')->nullable();

            // Opportunity Information
            $table->decimal('opportunity_value', 12, 2)->nullable()->comment('Valeur estimée de l\'opportunité');
            $table->decimal('opportunity_probability', 5, 2)->nullable()->comment('Probabilité en %');
            $table->string('opportunity_stage')->nullable()->comment('Étape de l\'opportunité');
            $table->text('opportunity_notes')->nullable();

            // Suivi du workflow
            $table->string('current_step')->default('prospect')->comment('Étape actuelle du processus');
            $table->string('step_status')->default('pending')->comment('Statut de l\'étape : pending, validated, rejected...');
            $table->datetime('validation_date')->nullable()->comment('Date de validation de l\'étape');
            $table->text('workflow_notes')->nullable()->comment('Remarques sur le suivi de la mission');

            // Relations - Ajout des clés étrangères APRÈS la création de la table
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('property_id')->nullable();
            $table->unsignedBigInteger('audit_id')->nullable();
            $table->unsignedBigInteger('financing_id')->nullable();
            $table->unsignedBigInteger('work_id')->nullable();
            $table->unsignedBigInteger('completion_id')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        // Ajout des contraintes de clés étrangères APRÈS la création de toutes les tables
        Schema::table('ream_mar_missions', function (Blueprint $table) {
            $table->foreign('client_id')->references('id')->on('ream_mar_clients')->onDelete('set null');
            $table->foreign('property_id')->references('id')->on('ream_mar_properties')->onDelete('set null');
            $table->foreign('audit_id')->references('id')->on('ream_mar_audits')->onDelete('set null');
            $table->foreign('financing_id')->references('id')->on('ream_mar_financings')->onDelete('set null');
            $table->foreign('work_id')->references('id')->on('ream_mar_works')->onDelete('set null');
            $table->foreign('completion_id')->references('id')->on('ream_mar_completions')->onDelete('set null');
            
            // CRM foreign keys
            $table->foreign('pipeline_id')->references('id')->on('crm_pipelines')->onDelete('set null');
            $table->foreign('pipeline_stage_id')->references('id')->on('crm_pipeline_stages')->onDelete('set null');
        });
    }

    public function down(): void
    {
        // Important : supprimer d'abord la table avec les foreign keys
        Schema::dropIfExists('ream_mar_missions');
        Schema::dropIfExists('ream_mar_clients');
        Schema::dropIfExists('ream_mar_properties');
        Schema::dropIfExists('ream_mar_audits');
        Schema::dropIfExists('ream_mar_financings');
        Schema::dropIfExists('ream_mar_works');
        Schema::dropIfExists('ream_mar_completions');
    }
};