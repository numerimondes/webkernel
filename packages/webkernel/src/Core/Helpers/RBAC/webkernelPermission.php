<?php

if (!function_exists('webkernelPermission')) {
    function webkernelPermission(string $action, ?string $policyClass = null): bool
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }

        // Si la policy class est fournie directement, l'utiliser
        if ($policyClass) {
            return $user->hasWebkernelPermission($policyClass, $action);
        }

        // Obtenir la policy class depuis la stack trace
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
        
        // Chercher la policy class dans la stack trace
        $policyClass = null;
        foreach ($backtrace as $trace) {
            if (isset($trace['class']) && str_contains($trace['class'], 'Policy')) {
                $policyClass = $trace['class'];
                break;
            }
        }
        
        if (!$policyClass) {
            // Debug: log pour voir ce qui se passe
            \Log::warning('webkernelPermission: Policy class not found', [
                'action' => $action,
                'backtrace' => array_map(function($trace) {
                    return [
                        'class' => $trace['class'] ?? null,
                        'function' => $trace['function'] ?? null,
                        'file' => $trace['file'] ?? null,
                        'line' => $trace['line'] ?? null,
                    ];
                }, array_slice($backtrace, 0, 5)) // Limiter à 5 pour éviter les logs trop longs
            ]);
            return false;
        }

        return $user->hasWebkernelPermission($policyClass, $action);
    }
}