<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apt_global_enums', function (Blueprint $table) {
            $table->id();

            // Core fields
            $table->string('type', 50)->index();
            $table->string('key', 50);
            $table->string('label_key', 100);
            $table->string('default_label', 100);

            // Optional fields
            $table->string('description_key', 100)->nullable();
            $table->string('icon', 50)->nullable();
            $table->string('css_class', 50)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            // Hierarchy
            $table->unsignedBigInteger('parent_id')->nullable();

            // Metadata - handle JSON compatibility across databases
            if ($this->supportsJson()) {
                $table->json('metadata')->nullable();
                $table->json('contexts')->nullable();
            } else {
                $table->text('metadata')->nullable();
                $table->text('contexts')->nullable();
            }

            // Relations
            $table->string('model_type', 100)->nullable()->index();

            $table->timestamps();

            // Indexes
            $table->unique(['type', 'key']);
            $table->index(['type', 'is_active', 'sort_order']);
            $table->index('parent_id');
        });

        // Add self-referencing foreign key only if database supports it
        if ($this->supportsForeignKeys()) {
            Schema::table('apt_global_enums', function (Blueprint $table) {
                $table->foreign('parent_id')
                    ->references('id')
                    ->on('apt_global_enums')
                    ->onDelete('cascade');
            });
        }

        // Insert initial data
        $this->insertInitialData();
    }

    public function down(): void
    {
        Schema::dropIfExists('apt_global_enums');
    }

    private function insertInitialData(): void
    {
        $timestamp = now();

        // Define all records with consistent structure
        $enums = [
            // Company types
            [
                'type' => 'company_type',
                'key' => 'individual',
                'label_key' => 'enum_company_type_individual',
                'default_label' => 'Individual',
                'description_key' => null,
                'icon' => 'user',
                'css_class' => 'badge-primary',
                'sort_order' => 1,
                'is_active' => true,
                'parent_id' => null,
                'metadata' => null,
                'contexts' => null,
                'model_type' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'type' => 'company_type',
                'key' => 'llc',
                'label_key' => 'enum_company_type_llc',
                'default_label' => 'LLC',
                'description_key' => null,
                'icon' => 'building',
                'css_class' => 'badge-success',
                'sort_order' => 2,
                'is_active' => true,
                'parent_id' => null,
                'metadata' => null,
                'contexts' => null,
                'model_type' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'type' => 'company_type',
                'key' => 'corporation',
                'label_key' => 'enum_company_type_corporation',
                'default_label' => 'Corporation',
                'description_key' => null,
                'icon' => 'building-2',
                'css_class' => 'badge-info',
                'sort_order' => 3,
                'is_active' => true,
                'parent_id' => null,
                'metadata' => $this->encodeJson(['requires_registration' => true]),
                'contexts' => null,
                'model_type' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],

            // Status
            [
                'type' => 'status',
                'key' => 'active',
                'label_key' => 'enum_status_active',
                'default_label' => 'Active',
                'description_key' => null,
                'icon' => 'circle-check',
                'css_class' => 'text-success',
                'sort_order' => 1,
                'is_active' => true,
                'parent_id' => null,
                'metadata' => null,
                'contexts' => $this->encodeJson(['company', 'user', 'product']),
                'model_type' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'type' => 'status',
                'key' => 'inactive',
                'label_key' => 'enum_status_inactive',
                'default_label' => 'Inactive',
                'description_key' => null,
                'icon' => 'x-circle',
                'css_class' => 'text-danger',
                'sort_order' => 2,
                'is_active' => true,
                'parent_id' => null,
                'metadata' => null,
                'contexts' => $this->encodeJson(['company', 'user', 'product']),
                'model_type' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],

            // Priority with metadata
            [
                'type' => 'priority',
                'key' => 'urgent',
                'label_key' => 'enum_priority_urgent',
                'default_label' => 'Urgent',
                'description_key' => null,
                'icon' => 'alert-triangle',
                'css_class' => 'bg-red-500',
                'sort_order' => 1,
                'is_active' => true,
                'parent_id' => null,
                'metadata' => $this->encodeJson(['sla_hours' => 4]),
                'contexts' => null,
                'model_type' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'type' => 'priority',
                'key' => 'high',
                'label_key' => 'enum_priority_high',
                'default_label' => 'High',
                'description_key' => null,
                'icon' => 'arrow-up',
                'css_class' => 'bg-orange-500',
                'sort_order' => 2,
                'is_active' => true,
                'parent_id' => null,
                'metadata' => $this->encodeJson(['sla_hours' => 8]),
                'contexts' => null,
                'model_type' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'type' => 'priority',
                'key' => 'medium',
                'label_key' => 'enum_priority_medium',
                'default_label' => 'Medium',
                'description_key' => null,
                'icon' => 'minus',
                'css_class' => 'bg-yellow-500',
                'sort_order' => 3,
                'is_active' => true,
                'parent_id' => null,
                'metadata' => $this->encodeJson(['sla_hours' => 24]),
                'contexts' => null,
                'model_type' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'type' => 'priority',
                'key' => 'low',
                'label_key' => 'enum_priority_low',
                'default_label' => 'Low',
                'description_key' => null,
                'icon' => 'arrow-down',
                'css_class' => 'bg-blue-500',
                'sort_order' => 4,
                'is_active' => true,
                'parent_id' => null,
                'metadata' => $this->encodeJson(['sla_hours' => 72]),
                'contexts' => null,
                'model_type' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ];

        // Insert data in chunks for better performance and compatibility
        $chunks = array_chunk($enums, 50);
        foreach ($chunks as $chunk) {
            DB::table('apt_global_enums')->insert($chunk);
        }
    }

    private function supportsJson(): bool
    {
        $driver = DB::getDriverName();
        return in_array($driver, ['mysql', 'pgsql'], true);
    }

    private function supportsForeignKeys(): bool
    {
        $driver = DB::getDriverName();
        return in_array($driver, ['mysql', 'pgsql', 'sqlsrv'], true);
    }

    private function encodeJson(?array $data): ?string
    {
        return $data ? json_encode($data) : null;
    }
};
