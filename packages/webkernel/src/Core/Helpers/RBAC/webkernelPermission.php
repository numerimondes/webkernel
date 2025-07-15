<?php

if (!function_exists('webkernelPermission')) {
    function webkernelPermission(string $action): bool
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }

        // Obtenir la policy class depuis la stack trace
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $policyClass = $backtrace[1]['class'] ?? null;
        
        if (!$policyClass) {
            return false;
        }

        return $user->hasWebkernelPermission($policyClass, $action);
    }
}