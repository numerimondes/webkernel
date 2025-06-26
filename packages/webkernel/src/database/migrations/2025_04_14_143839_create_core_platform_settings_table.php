<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * This table stores platform-level settings for multi or single tenant apps.
 *
 * Some of these settings directly map to the software update URL used
 * by the CLI or update engine, such as:
 *
 * Example:
 * https://software-licence.numerimondes.com/server
 *    ?request-type=download-update
 *    &app=ream
 *    &version=1.5.3
 *    &channel=stable
 *    &origin=artisan
 *    &licence_key=...
 *    &otp=UPDATER--...
 *    &tenant_id=tnnt_8491
 *    &project_id=proj_XYZ123
 *    &environment=production
 *    &instance_hash=9a7b3d192cfc
 *    &hostname=school42.numerimondes.net
 *    &php=8.3
 *    &laravel=12.7
 *    &webkernel=1.4.2
 *    &platform=ream-core
 *    &update_mode=manual
 *    &cli_version=1.2.0
 *
 * License key formats used (non-guessable, structured):
 *
 *  PR-DI-CRM1-CL88-92K4FJQWM7X8-A9F2
 *  SB-MO-REAM-CL23-ABCD92K4FJQWM7X8-B92T
 *  SB-YR-CRM1-CL05-48DSQXPKA9F2-03TY
 *  PR-RE-VENDOR1-CL77-XPK92K4WQWM7-A7B3
 *  LE-MO-CRM1-CL55-RA9BZQKD-XW3Y
 *  LE-YR-REAM-CL05-FTR29XA9-99AF
 *  BD-EDU-REAMPACK1-CL91-QXZ81WZA-DSS3
 *  TR-FN-REAM-CL19-TRIAL2FULL-YX91
 *  IN-SLA-UNIV-MED-CL01-YZ492A9F
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('core_platform_settings', function (Blueprint $table) {
            $table->id()->comment('Primary key for the settings table');
            $table->string('settings_reference', 100)->comment('Unique reference for each setting (e.g., PLATFORM_LICENCE, PLATFORM_ENVIRONMENT)');
            $table->string('value', 255)->nullable()->comment('Actual value for the setting (e.g., license key, OTP token, hostname, instance hash)');
            $table->string('name_lang_key', 50)->comment('Translation key for the setting name (e.g., settings.platform_licence)');
            $table->string('description_lang_key', 255)->nullable()->comment('Translation key for a short description (e.g., settings.platform_licence_desc)');
            $table->unsignedBigInteger('tenant_id')->default(1)->comment('Tenant or customer ID in multi-tenant context');
            $table->timestamp('created_at')->nullable()->comment('Date and time when the setting was created');
            $table->timestamp('updated_at')->nullable()->comment('Date and time when the setting was last updated');

            // Indexes
            $table->unique(['settings_reference', 'tenant_id'], 'settings_ref_tenant_unique');
            $table->index('tenant_id', 'tenant_id_index');
        });

        // Insert example license key for PLATFORM_LICENCE
        DB::statement("
            INSERT INTO `core_platform_settings` (
                `settings_reference`,
                `value`,
                `name_lang_key`,
                `description_lang_key`,
                `tenant_id`,
                `created_at`,
                `updated_at`
            ) VALUES (
                'PLATFORM_LICENCE',
                'TEST',
                'settings_platform_licence',
                'settings_platform_licence_desc',
                1,
                NOW(),
                NOW()
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('core_platform_settings')) {
            \Illuminate\Support\Facades\Log::info('Dropping core_platform_settings table');
            Schema::drop('core_platform_settings');
        }
    }
};
