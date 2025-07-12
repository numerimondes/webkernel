<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ream_mar_clients', function (Blueprint $table) {
            $table->id();
            $table->enum('civility', ['Mr', 'Mrs']);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('folder_name');

            $table->string('fiscal_address');
            $table->string('fiscal_postal_code');
            $table->string('fiscal_city');
            $table->string('fiscal_country')->default('France');

            $table->json('phones');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->boolean('can_login')->default(0);

            $table->enum('household_status', [
                'MODEST_HOUSEHOLDS',
                'VERY_MODEST_HOUSEHOLDS',
                'INTERMEDIATE_HOUSEHOLDS',
                'SUPERIOR_HOUSEHOLDS'
            ])->nullable();

            $table->enum('usage_type', [
                'primary_residence',
                'owner_occupier',
                'owner_landlord',
                'lender',
                'free_title_occupant',
                'usufructuary',
                'tenant',
                'bare_owner',
                'sci_owner_occupier',
                'sci_owner_landlord'
            ])->nullable();

            $table->timestamps();
        });

        Schema::create('ream_mar_project_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('ream_mar_clients')->onDelete('cascade');
            $table->string('street');
            $table->string('postal_code');
            $table->string('city');
            $table->string('country')->default('France');
            $table->timestamps();
        });

        Schema::create('ream_mar_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('ream_mar_clients')->onDelete('cascade');
            $table->string('project_name');
            $table->date('contract_date');

            $table->decimal('mission_amount_excl_tax', 10, 2)->default(1666.67);
            $table->decimal('mission_amount_incl_tax', 10, 2)->default(2000.00);
            $table->decimal('vat', 10, 2)->default(333.33);
            $table->decimal('first_installment', 10, 2)->nullable();

            $table->boolean('mar_administrative_agent')->default(false);
            $table->boolean('mar_financial_agent')->default(false);

            $table->string('company_name');
            $table->string('mar_approval_number');
            $table->string('siret_number');
            $table->string('head_office_address');
            $table->string('head_office_postal_code');
            $table->string('head_office_city');
            $table->string('company_phone');
            $table->string('company_email');
            $table->string('insurer');
            $table->string('insurance_policy_number');

            $table->enum('signature_provider', ['docusign', 'docapost', 'yousign', 'handwritten_signature']);
            $table->string('signature_provider_id')->nullable();
            $table->text('mar_signature_link')->nullable();
            $table->text('client_signature_link')->nullable();

            $table->timestamps();
        });

        Schema::create('ream_mar_mandates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('ream_mar_clients')->onDelete('cascade');

            $table->integer('household_composition')->nullable();
            $table->decimal('reference_tax_income', 10, 2)->nullable();
            $table->enum('household_category', ['very_modest', 'modest', 'intermediate', 'superior'])->nullable();

            $table->enum('cerfa_type', ['15923_01', '16089_02'])->nullable();

            $table->timestamps();
        });

        Schema::create('ream_mar_external_agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('ream_mar_clients')->onDelete('cascade');
            $table->string('company_name');
            $table->string('siret_number');
            $table->string('agent_email');
            $table->string('registration_number')->nullable();
            $table->text('mandate_document')->nullable();
            $table->boolean('administrative_agent')->default(false);
            $table->boolean('financial_agent')->default(false);
            $table->timestamps();
        });

        Schema::create('ream_mar_client_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('ream_mar_clients')->onDelete('cascade');
            $table->string('action');
            $table->text('description')->nullable();
            $table->json('data')->nullable();
            $table->timestamp('created_at');
        });

        Schema::create('ream_mar_client_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('ream_mar_clients')->onDelete('cascade');
            $table->string('document_type');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->bigInteger('file_size');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ream_mar_client_documents');
        Schema::dropIfExists('ream_mar_client_actions');
        Schema::dropIfExists('ream_mar_external_agents');
        Schema::dropIfExists('ream_mar_mandates');
        Schema::dropIfExists('ream_mar_contracts');
        Schema::dropIfExists('ream_mar_project_addresses');
        Schema::dropIfExists('ream_mar_clients');
    }
};
