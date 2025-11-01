<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\AccessControl\Commands;

use Illuminate\Console\Command;
use Webkernel\Aptitudes\AccessControl\Models\PermissionGroup;

class CreateSuperAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'access:create-superadmin {--user= : User ID or email to assign superadmin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create superadmin permission group and optionally assign to a user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Creating superadmin permission group...');

        try {
            // Create or get superadmin group
            $superadmin = PermissionGroup::firstOrCreate(
                ['slug' => 'superadmin'],
                [
                    'name' => 'Super Administrator',
                    'description' => 'Full system access with all permissions',
                    'is_system' => true,
                    'priority' => 999
                ]
            );

            $this->info('Superadmin group created/found: ' . $superadmin->name);

            // Assign user if specified
            if ($userIdentifier = $this->option('user')) {
                $userModel = config('auth.providers.users.model', \App\Models\User::class);

                // Find user by ID or email
                $user = is_numeric($userIdentifier)
                    ? $userModel::find($userIdentifier)
                    : $userModel::where('email', $userIdentifier)->first();

                if (!$user) {
                    $this->error("User not found: {$userIdentifier}");
                    return Command::FAILURE;
                }

                // Check if user has the trait
                if (!method_exists($user, 'assignPermissionGroup')) {
                    $this->error('User model does not have HasPermissions trait!');
                    $this->warn('Add this to your User model:');
                    $this->line('use \Webkernel\Aptitudes\AccessControl\Models\HasPermissions;');
                    return Command::FAILURE;
                }

                // Check if already assigned
                if ($user->permissionGroups()->where('permission_group_id', $superadmin->id)->exists()) {
                    $this->warn("User {$user->email} already has superadmin permission group");
                    return Command::SUCCESS;
                }

                $user->assignPermissionGroup($superadmin, 'Assigned via command');
                $this->info("Superadmin assigned to user: {$user->email}");
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to create superadmin: ' . $e->getMessage());

            if ($this->option('verbose')) {
                $this->error($e->getTraceAsString());
            }

            return Command::FAILURE;
        }
    }
}
