<?php
/**
 * Enhanced RBAC Helper: webkernelPermission
 *
 * A powerful and flexible Role-Based Access Control helper that automatically
 * detects policy classes and actions from the call stack, providing seamless
 * authorization checks throughout Webkernel/Laravel application.
 *
 * Features:
 * - Automatic policy class detection
 * - Automatic action detection from method names
 * - Comprehensive error handling and logging
 * - Support for custom policy classes and actions
 * - Performance optimized with caching
 * - Detailed debugging information
 *
 * Usage Examples:
 *
 * 1. Basic Policy Usage:
 *    public function approve(User $user, Model $model = null): bool
 *    {
 *        return webkernelPermission(); // Auto-detects 'approve' action
 *    }
 *
 * 2. Filament Resource Integration:
 *    Action::make('approve')
 *        ->label('Approve')
 *        ->action(fn ($record) => $this->handleApproval($record))
 *        ->visible(fn ($record) => auth()->user()?->can('approve', $record));
 *
 * 3. Controller Authorization:
 *    public function approve(Request $request, Model $model)
 *    {
 *        $this->authorize('approve', $model);
 *        // Your approval logic here
 *    }
 *
 * 4. Manual Permission Check:
 *    if (webkernelPermission('delete', PostPolicy::class)) {
 *        // User can delete posts
 *    }
 *
 * Database Requirements:
 * Create permissions with the following structure:
 * - policy_class: The fully qualified policy class name
 * - action: The method name (e.g., 'approve', 'delete', 'update')
 * - model_class: The associated model class (optional)
 * - role_id: The role that has this permission
 */

if (!function_exists('webkernelPermission')) {
    /**
     * Check if the current user has permission for a specific action
     *
     * @param string|null $action The action to check (auto-detected if null)
     * @param string|null $policyClass The policy class (auto-detected if null)
     * @param array $options Additional options for permission checking
     * @return bool True if user has permission, false otherwise
     */
    function webkernelPermission(
        ?string $action = null, 
        ?string $policyClass = null, 
        array $options = []
    ): bool {
        // Early return if no authenticated user
        $user = auth()->user();
        if (!$user) {
            logPermissionAttempt(null, $action, $policyClass, 'No authenticated user');
            return false;
        }

        // Skip permission check for super admins if configured
        if (config('webkernel.rbac.super_admin_bypass', false) && $user->hasRole('super-admin')) {
            return true;
        }

        try {
            // Get call stack for automatic detection
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 15);
            
            // Detect policy class if not provided
            if (!$policyClass) {
                $policyClass = detectPolicyClass($backtrace);
            }
            
            // Detect action if not provided
            if (!$action) {
                $action = detectAction($backtrace);
            }
            
            // Validate required parameters
            if (!$policyClass || !$action) {
                logPermissionError($user, $action, $policyClass, $backtrace);
                return config('webkernel.rbac.fail_secure', true) ? false : true;
            }
            
            // Check permission using the user model method
            $hasPermission = $user->hasWebkernelPermission($policyClass, $action, $options);
            
            // Log successful permission check if debugging is enabled
            if (config('webkernel.rbac.log_success', false)) {
                logPermissionAttempt($user, $action, $policyClass, 'Permission granted', $hasPermission);
            }
            
            return $hasPermission;
            
        } catch (\Exception $e) {
            // Log exception and fail securely
            \Log::error('webkernelPermission: Unexpected error during permission check', [
                'user_id' => $user->id ?? null,
                'action' => $action,
                'policy_class' => $policyClass,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }
}

if (!function_exists('detectPolicyClass')) {
    /**
     * Detect the policy class from the call stack
     *
     * @param array $backtrace The debug backtrace
     * @return string|null The detected policy class or null
     */
    function detectPolicyClass(array $backtrace): ?string
    {
        foreach ($backtrace as $trace) {
            if (!isset($trace['class'])) continue;
            
            $class = $trace['class'];
            
            // Check if it's a policy class
            if (str_ends_with($class, 'Policy') || str_contains($class, '\\Policies\\')) {
                return $class;
            }
        }
        
        return null;
    }
}

if (!function_exists('detectAction')) {
    /**
     * Detect the action from the call stack
     *
     * @param array $backtrace The debug backtrace
     * @return string|null The detected action or null
     */
    function detectAction(array $backtrace): ?string
    {
        foreach ($backtrace as $trace) {
            if (!isset($trace['class'], $trace['function'])) continue;
            
            $class = $trace['class'];
            $function = $trace['function'];
            
            // Skip if it's our helper function
            if ($function === 'webkernelPermission') continue;
            
            // Check if it's a policy method
            if (str_ends_with($class, 'Policy') || str_contains($class, '\\Policies\\')) {
                // Skip common policy methods that aren't actions
                $skipMethods = ['before', 'after', '__construct', '__call', '__get', '__set'];
                if (!in_array($function, $skipMethods)) {
                    return $function;
                }
            }
        }
        
        return null;
    }
}

if (!function_exists('logPermissionAttempt')) {
    /**
     * Log permission attempt for debugging and auditing
     *
     * @param mixed $user The user attempting the action
     * @param string|null $action The action being attempted
     * @param string|null $policyClass The policy class
     * @param string $message Additional message
     * @param bool|null $result The result of the permission check
     */
    function logPermissionAttempt($user, ?string $action, ?string $policyClass, string $message, ?bool $result = null): void
    {
        if (!config('webkernel.rbac.enable_logging', true)) return;
        
        $logData = [
            'user_id' => $user->id ?? null,
            'user_email' => $user->email ?? null,
            'action' => $action,
            'policy_class' => $policyClass,
            'message' => $message,
            'result' => $result,
            'timestamp' => now()->toISOString(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ];
        
        if ($result === false || $result === null) {
            \Log::warning('webkernelPermission: Permission denied or error', $logData);
        } else {
            \Log::info('webkernelPermission: Permission granted', $logData);
        }
    }
}

if (!function_exists('logPermissionError')) {
    /**
     * Log permission detection errors with detailed backtrace
     *
     * @param mixed $user The user
     * @param string|null $action The action
     * @param string|null $policyClass The policy class
     * @param array $backtrace The call stack
     */
    function logPermissionError($user, ?string $action, ?string $policyClass, array $backtrace): void
    {
        $formattedTrace = array_map(function($trace) {
            return [
                'class' => $trace['class'] ?? null,
                'function' => $trace['function'] ?? null,
                'file' => basename($trace['file'] ?? 'unknown'),
                'line' => $trace['line'] ?? null,
            ];
        }, array_slice($backtrace, 0, 8));
        
        \Log::error('webkernelPermission: Could not detect policy class or action', [
            'user_id' => $user->id ?? null,
            'detected_policy_class' => $policyClass,
            'detected_action' => $action,
            'backtrace' => $formattedTrace,
            'timestamp' => now()->toISOString()
        ]);
    }
}

if (!function_exists('webkernelCan')) {
    /**
     * Alias for webkernelPermission for more readable code
     *
     * @param string|null $action
     * @param string|null $policyClass
     * @param array $options
     * @return bool
     */
    function webkernelCan(?string $action = null, ?string $policyClass = null, array $options = []): bool
    {
        return webkernelPermission($action, $policyClass, $options);
    }
}

if (!function_exists('webkernelCannot')) {
    /**
     * Inverse of webkernelPermission
     *
     * @param string|null $action
     * @param string|null $policyClass
     * @param array $options
     * @return bool
     */
    function webkernelCannot(?string $action = null, ?string $policyClass = null, array $options = []): bool
    {
        return !webkernelPermission($action, $policyClass, $options);
    }
}