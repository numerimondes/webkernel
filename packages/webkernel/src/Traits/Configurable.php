<?php

namespace Webkernel\Traits;

use Exception;

trait Configurable
{
    protected $config;

    /**
     * Initialize configuration dynamically from config/webkernel.php or package default.
     *
     * @throws Exception if configuration is not found.
     */
    protected function initializeConfig()
    {
        // Priority 1: Laravel main config (config/webkernel.php)
        $this->config = config('webkernel', []);

        // Priority 2: Package default config
        if (empty($this->config)) {
            $packageConfigPath = base_path('packages/webkernel/src/config/webkernel.php');
            if (file_exists($packageConfigPath)) {
                $packageConfig = include $packageConfigPath;
                config(['webkernel' => $packageConfig]);
                $this->config = config('webkernel', []);
            }
        }

        // Ensure configuration exists
        if (empty($this->config)) {
            throw new Exception('WebKernel configuration not found. Package config should always be available.');
        }

        // Validate critical section
        $this->validateConfig();
    }

    /**
     * Validate only the prohibit_commands section to ensure valid classes and settings.
     *
     * @throws Exception if prohibit_commands is invalid.
     */
    protected function validateConfig()
    {
        // Only validate prohibit_commands (fixed name)
        if (!isset($this->config['prohibit_commands']) || !is_array($this->config['prohibit_commands'])) {
            throw new Exception('The "prohibit_commands" configuration is required and must be an array.');
        }

        foreach ($this->config['prohibit_commands'] as $command => $settings) {
            if (!isset($settings['class']) || !is_string($settings['class']) || !class_exists($settings['class'])) {
                throw new Exception("Invalid or non-existent class for command '$command': " . ($settings['class'] ?? 'missing'));
            }
            if (!isset($settings['prohibited']) || !is_bool($settings['prohibited'])) {
                throw new Exception("The 'prohibited' key for command '$command' must be a boolean.");
            }
        }

        // No validation for other sections to support dynamic config growth
    }

    /**
     * Get configuration value dynamically using dot notation.
     *
     * @param string|null $key Config key (dot notation supported, null returns full config).
     * @param mixed $default Default value if key is not found.
     * @return mixed
     */
    public function getConfig($key = null, $default = null)
    {
        if (!$this->config) {
            $this->initializeConfig();
        }

        return $key === null ? $this->config : data_get($this->config, $key, $default);
    }
}
