<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique();
            $table->string('mobile')->nullable()->unique();
            $table->string('whatsapp')->nullable()->unique();
            $table->string('timezone')->nullable();
            $table->string('user_lang', 2)->default('en');
            $table->boolean('forceChangePassword')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_banned')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('marketing_callable')->default(true);
            $table->boolean('marketing_whatsappable')->default(true);
            $table->boolean('marketing_smsable')->default(true);
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

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        if (Schema::hasTable('users')) {
            if (Schema::hasColumn('users', 'created_by')) {
                try {
                    DB::statement('ALTER TABLE `users` DROP FOREIGN KEY `users_created_by_foreign`');
                } catch (\Throwable $e) {}
            }

            Schema::table('users', function (Blueprint $table) {
                foreach ([
                    'username',
                    'mobile',
                    'whatsapp',
                    'timezone',
                    'user_lang',
                    'forceChangePassword',
                    'is_active',
                    'is_banned',
                    'created_by',
                    'marketing_callable',
                    'marketing_whatsappable',
                    'marketing_smsable',
                    'belongs_to',
                ] as $column) {
                    if (Schema::hasColumn('users', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        Schema::dropIfExists('history_sessions');

        Schema::enableForeignKeyConstraints();
    }
};
