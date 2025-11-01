<?php

namespace Webkernel\Aptitudes\I18n\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->createLanguagesTable();
        $this->createTranslationCategoriesTable();
        $this->createTranslationsTable();
        $this->seedDefaultLanguages();
    }

    public function down(): void
    {
        // Drop tables in reverse order to respect foreign key constraints
        $translations = APTITUDE_DB_PREFIX . 'translations';
        $categories = APTITUDE_DB_PREFIX . 'translation_categories';
        $languages = APTITUDE_DB_PREFIX . 'languages';

        // Disable foreign key checks for databases that support it
        $this->disableForeignKeyChecks();

        try {
            if (Schema::hasTable($translations)) {
                Schema::dropIfExists($translations);
            }
            if (Schema::hasTable($categories)) {
                Schema::dropIfExists($categories);
            }
            if (Schema::hasTable($languages)) {
                Schema::dropIfExists($languages);
            }
        } finally {
            // Re-enable foreign key checks
            $this->enableForeignKeyChecks();
        }
    }

    private function createLanguagesTable(): void
    {
        $table = APTITUDE_DB_PREFIX . 'languages';

        if (Schema::hasTable($table)) {
            return;
        }

        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->index(); // en, fr, ar
            $table->string('iso', 10)->nullable()->index(); // en-US, fr-FR, ar-MA
            $table->string('label')->index();
            $table->string('native_label')->nullable(); // English, Français, العربية
            $table->string('direction', 3)->default('ltr'); // ltr, rtl
            $table->boolean('active')->default(true)->index();
            $table->boolean('is_default')->default(false)->index();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->json('metadata')->nullable(); // country, region, currency, etc.
            $table->timestamps();

            // Indexes for performance
            $table->index(['code', 'tenant_id']);
            $table->index(['iso', 'tenant_id']);
            $table->index(['active', 'tenant_id']);
            $table->index(['is_default', 'tenant_id']);

            // Unique constraints
            $table->unique(['tenant_id', 'code'], 'uniq_tenant_code');
            // Only add ISO unique constraint if database supports it with nullable values
            if ($this->supportsNullableUnique()) {
                $table->unique(['tenant_id', 'iso'], 'uniq_tenant_iso');
            }
        });
    }

    private function createTranslationCategoriesTable(): void
    {
        $table = APTITUDE_DB_PREFIX . 'translation_categories';

        if (Schema::hasTable($table)) {
            return;
        }

        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('slug')->index(); // Changed from unique to index for better compatibility
            $table->string('description')->nullable();
            $table->string('app')->default('core')->index();
            $table->string('module')->nullable()->index();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->boolean('is_system')->default(false);
            $table->timestamps();

            $table->unique(['tenant_id', 'slug'], 'uniq_tenant_category_slug');
            $table->index(['app', 'module']);
        });
    }

    private function createTranslationsTable(): void
    {
        $table = APTITUDE_DB_PREFIX . 'translations';

        if (Schema::hasTable($table)) {
            return;
        }

        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('language_id');
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('reference')->index(); // welcome_message, auth.failed

            // Use text instead of longText for better SQLite compatibility
            if ($this->getDriverName() === 'sqlite') {
                $table->text('value');
            } else {
                $table->longText('value'); // Support for large content like page content
            }

            $table->string('app')->default('core')->index();
            $table->string('theme')->default('default')->index();
            $table->string('module')->nullable()->index(); // website-builder, ecommerce, etc.
            $table->unsignedBigInteger('category_id')->nullable();

            // Use string instead of enum for better database compatibility
            $table->string('content_type')->default('text')->index();

            $table->json('metadata')->nullable(); // pluralization rules, context, etc.
            $table->boolean('is_system')->default(false)->index(); // System vs user-editable
            $table->boolean('needs_review')->default(false)->index();
            $table->timestamp('last_used_at')->nullable()->index();
            $table->timestamps();

            // Performance indexes
            $table->index(['reference', 'app', 'theme']);
            $table->index(['language_id', 'app', 'theme']);
            $table->index(['tenant_id', 'app', 'module']);
            $table->index(['content_type', 'app']);
            $table->index(['is_system', 'needs_review']);

            // Unique constraint to prevent duplicates with shorter name
            $table->unique(['language_id', 'reference', 'app', 'theme', 'module'], 'uniq_lang_trans');

            // Add foreign keys only if the database supports them
            if ($this->supportsForeignKeys()) {
                $table->foreign('language_id')
                    ->references('id')
                    ->on(APTITUDE_DB_PREFIX . 'languages')
                    ->onDelete('cascade');

                $table->foreign('category_id')
                    ->references('id')
                    ->on(APTITUDE_DB_PREFIX . 'translation_categories')
                    ->onDelete('set null');
            }
        });

        // Add content_type check constraint for databases that support it
        $this->addContentTypeConstraint();
    }

    private function supportsForeignKeys(): bool
    {
        $driver = $this->getDriverName();
        return in_array($driver, ['mysql', 'pgsql', 'sqlsrv'], true);
    }

    private function supportsNullableUnique(): bool
    {
        $driver = $this->getDriverName();
        // SQLite has issues with nullable unique constraints
        return $driver !== 'sqlite';
    }

    private function getDriverName(): string
    {
        return DB::getDriverName();
    }

    private function disableForeignKeyChecks(): void
    {
        $driver = $this->getDriverName();

        switch ($driver) {
            case 'mysql':
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                break;
            case 'pgsql':
                // PostgreSQL doesn't have a global foreign key check disable
                // We'll handle this table by table if needed
                break;
            case 'sqlsrv':
                // SQL Server handles this differently, usually not needed for DROP IF EXISTS
                break;
            case 'sqlite':
                DB::statement('PRAGMA foreign_keys = OFF');
                break;
        }
    }

    private function enableForeignKeyChecks(): void
    {
        $driver = $this->getDriverName();

        switch ($driver) {
            case 'mysql':
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
                break;
            case 'pgsql':
                // Nothing to do for PostgreSQL
                break;
            case 'sqlsrv':
                // Nothing to do for SQL Server
                break;
            case 'sqlite':
                DB::statement('PRAGMA foreign_keys = ON');
                break;
        }
    }

    private function addContentTypeConstraint(): void
    {
        $driver = $this->getDriverName();
        $table = APTITUDE_DB_PREFIX . 'translations';

        // Add check constraint for content_type values (where supported)
        switch ($driver) {
            case 'mysql':
                // MySQL 8.0+ supports check constraints
                try {
                    DB::statement("ALTER TABLE `{$table}` ADD CONSTRAINT chk_content_type CHECK (content_type IN ('text', 'html', 'markdown', 'json'))");
                } catch (\Exception $e) {
                    // Ignore if check constraints are not supported (MySQL < 8.0)
                }
                break;
            case 'pgsql':
                DB::statement("ALTER TABLE \"{$table}\" ADD CONSTRAINT chk_content_type CHECK (content_type IN ('text', 'html', 'markdown', 'json'))");
                break;
            case 'sqlsrv':
                DB::statement("ALTER TABLE [{$table}] ADD CONSTRAINT chk_content_type CHECK (content_type IN ('text', 'html', 'markdown', 'json'))");
                break;
            case 'sqlite':
                // SQLite check constraints need to be added during table creation
                // We'll handle validation in the application layer
                break;
        }
    }

    /**
     * Seed default languages (English, French, Arabic)
     */
    private function seedDefaultLanguages(): void
    {
        $table = APTITUDE_DB_PREFIX . 'languages';
        $tenantId = 1; // Default tenant ID

        // Check if languages already exist to avoid duplicates
        $existingCount = DB::table($table)->where('tenant_id', $tenantId)->count();

        if ($existingCount > 0) {
            return; // Languages already seeded
        }

        $languages = [
            [
                'code' => 'en',
                'iso' => 'en-US',
                'label' => 'English',
                'native_label' => 'English',
                'direction' => 'ltr',
                'active' => true,
                'is_default' => true,
                'tenant_id' => $tenantId,
                'metadata' => json_encode([
                    'country' => 'US',
                    'region' => 'North America',
                    'currency' => 'USD'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'fr',
                'iso' => 'fr-FR',
                'label' => 'French',
                'native_label' => 'Français',
                'direction' => 'ltr',
                'active' => true,
                'is_default' => false,
                'tenant_id' => $tenantId,
                'metadata' => json_encode([
                    'country' => 'FR',
                    'region' => 'Europe',
                    'currency' => 'EUR'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'ar',
                'iso' => 'ar-SA',
                'label' => 'Arabic',
                'native_label' => 'العربية',
                'direction' => 'rtl',
                'active' => true,
                'is_default' => false,
                'tenant_id' => $tenantId,
                'metadata' => json_encode([
                    'country' => 'SA',
                    'region' => 'Middle East',
                    'currency' => 'SAR'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table($table)->insert($languages);
    }
};
