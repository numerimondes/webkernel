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
    // Permission groups (formerly roles)
    Schema::create('users_priv_permission_groups', function (Blueprint $table) {
      $table->id();
      $table->string('name')->unique();
      $table->string('slug')->unique();
      $table->text('description')->nullable();
      $table->boolean('is_system')->default(false); // System groups cannot be deleted
      $table->integer('priority')->default(0); // Higher priority groups override lower ones
      $table->timestamps();

      $table->index('slug');
      $table->index('is_system');
    });

    // Permissions
    Schema::create('users_priv_permissions', function (Blueprint $table) {
      $table->id();
      $table->string('module')->nullable(); // Module name for grouping, like "App" ou "I18n"
      $table->string('name')->unique(); // Full FQCN Inspired like: "View Users from App Module"
      $table->string('action'); // view, create, update, delete, etc.
      $table->string('model_class'); // Full namespace: App\Models\User
      $table->text('description')->nullable();
      $table->boolean('is_system')->default(false); // System permissions cannot be deleted
      $table->timestamps();

      $table->index('name');
      $table->index('action');
      $table->index('model_class');
      $table->index('module');
      $table->index('is_system');
    });

    // Pivot table: User <-> Permission Group
    Schema::create('users_priv_permission_group_user', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained('users', 'id', 'fk_pgu_user')->cascadeOnDelete();
      $table
        ->foreignId('permission_group_id')
        ->constrained('users_priv_permission_groups', 'id', 'fk_pgu_group')
        ->cascadeOnDelete();
      $table->timestamp('assigned_at')->nullable();
      $table->foreignId('assigned_by')->nullable()->constrained('users', 'id', 'fk_pgu_assigned_by')->nullOnDelete();
      $table->timestamp('expires_at')->nullable(); // Optional: temporary permissions
      $table->timestamps();

      $table->unique(['user_id', 'permission_group_id'], 'user_permission_group_unique');
      $table->index('expires_at');
    });

    // Pivot table: Permission Group <-> Permission
    Schema::create('users_priv_permission_group_permission', function (Blueprint $table) {
      $table->id();
      $table
        ->foreignId('permission_group_id')
        ->constrained('users_priv_permission_groups', 'id', 'fk_pgp_group')
        ->cascadeOnDelete();
      $table
        ->foreignId('permission_id')
        ->constrained('users_priv_permissions', 'id', 'fk_pgp_permission')
        ->cascadeOnDelete();
      $table->boolean('is_excluded')->default(false); // Allow exclusion of specific permissions
      $table->timestamps();

      $table->unique(['permission_group_id', 'permission_id'], 'group_permission_unique');
      $table->index('is_excluded');
    });

    // Direct user permissions (optional, for exceptions)
    Schema::create('users_priv_permission_user', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained('users', 'id', 'fk_pu_user')->cascadeOnDelete();
      $table
        ->foreignId('permission_id')
        ->constrained('users_priv_permissions', 'id', 'fk_pu_permission')
        ->cascadeOnDelete();
      $table->boolean('is_granted')->default(true); // true = grant, false = revoke
      $table->timestamp('assigned_at')->nullable();
      $table->foreignId('assigned_by')->nullable()->constrained('users', 'id', 'fk_pu_assigned_by')->nullOnDelete();
      $table->timestamp('expires_at')->nullable();
      $table->text('reason')->nullable(); // Why this direct permission was assigned
      $table->timestamps();

      $table->unique(['user_id', 'permission_id'], 'user_permission_unique');
      $table->index('is_granted');
      $table->index('expires_at');
    });

    // Audit log for permission changes
    Schema::create('users_priv_audit_logs', function (Blueprint $table) {
      $table->id();
      $table->string('event_type'); // permission_granted, permission_revoked, group_assigned, etc.
      $table->morphs('auditable'); // Can be User, PermissionGroup, Permission
      $table->foreignId('user_id')->nullable()->constrained('users', 'id', 'fk_audit_user')->nullOnDelete();
      $table->foreignId('performed_by')->nullable()->constrained('users', 'id', 'fk_audit_performer')->nullOnDelete();
      $table->json('old_values')->nullable();
      $table->json('new_values')->nullable();
      $table->string('ip_address')->nullable();
      $table->text('user_agent')->nullable();
      $table->text('reason')->nullable();
      $table->timestamp('created_at')->useCurrent();

      $table->index('event_type');
      $table->index('created_at');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('users_priv_audit_logs');
    Schema::dropIfExists('users_priv_permission_user');
    Schema::dropIfExists('users_priv_permission_group_permission');
    Schema::dropIfExists('users_priv_permission_group_user');
    Schema::dropIfExists('users_priv_permissions');
    Schema::dropIfExists('users_priv_permission_groups');
  }
};
