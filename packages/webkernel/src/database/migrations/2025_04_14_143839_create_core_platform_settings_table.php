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
        Schema::create('core_platform_settings', function (Blueprint $table) {
            $table->id();

            $table->string('settings_reference')->unique();
            $table->json('value')->nullable();
            $table->json('default_value')->nullable();

            $table->string('icon')->nullable();
            $table->string('name');
            $table->text('description')->nullable();

            // 'belongs_to' peut être renommé pour plus de clarté
            $table->integer('tenant_id')->default(1); // Identifiant du tenant (avant implémentation)

            $table->string('module')->nullable();

            $table->enum('type', [
                'tinyint',
                'smallint',
                'mediumint',
                'int',
                'bigint',
                'float',
                'double',
                'decimal',
                'char',
                'varchar',
                'text',
                'tinytext',
                'mediumtext',
                'longtext',
                'date',
                'datetime',
                'timestamp',
                'time',
                'year',
                'binary',
                'varbinary',
                'blob',
                'tinyblob',
                'mediumblob',
                'longblob',
                'enum',
                'set',
                'json',
                'point',
                'linestring',
                'polygon',
                'geometry',
                'geometrycollection',
                'string',
                'boolean',
                'array',
                'file',
                'uuid',
                'email',
                'url',
                'image',
                'currency',
                'phone'
            ])->default('string');

            $table->boolean('is_editable')->default(false);
            $table->integer('belongs_to')->default(1); // Tenant before implementation

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_platform_settings');
    }
};
