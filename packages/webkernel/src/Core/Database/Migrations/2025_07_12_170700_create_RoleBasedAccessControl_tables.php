<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rbac_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('module')->nullable();
            $table->string('namespace')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['name', 'module', 'namespace']);
            $table->index(['module', 'namespace']);
        });
        
        Schema::create('rbac_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('policy_class');
            $table->string('action');
            $table->string('model_class');
            $table->string('module')->nullable();
            $table->string('namespace')->nullable();
            $table->string('display_name')->nullable();
            $table->timestamps();
            
            $table->unique(['policy_class', 'action']);
            $table->index(['module', 'namespace']);
        });
        
        Schema::create('rbac_role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('rbac_roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('rbac_permissions')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['role_id', 'permission_id']);
        });
        
        Schema::create('rbac_user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('rbac_roles')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['user_id', 'role_id']);
        });
        
        Schema::create('rbac_user_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('rbac_permissions')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['user_id', 'permission_id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('rbac_user_permissions');
        Schema::dropIfExists('rbac_user_roles');
        Schema::dropIfExists('rbac_role_permissions');
        Schema::dropIfExists('rbac_permissions');
        Schema::dropIfExists('rbac_roles');
    }
};