<?php

namespace Webkernel\Aptitudes\Panels\Http\Requests;

/**
 * Purpose: Form request validation for updating existing panel configurations
 *
 * This request validates panel update data using dynamic schema analysis,
 * ensuring method parameters remain valid while allowing partial updates
 * of panel configurations.
 */

use Illuminate\Foundation\Http\FormRequest;
use Webkernel\Aptitudes\Panels\Helpers\PanelSchemaHelper;
use Filament\Panel;

class UpdatePanelsRequest extends FormRequest
{
    protected ?PanelSchemaHelper $schemaHelper = null;

    public function authorize(): bool
    {
        return true; // Implement your authorization logic
    }

    public function rules(): array
    {
        $panelId = $this->route('panel') ?? $this->input('id');

        return [
            'id' => "nullable|string|max:255|unique:apt_panels,id,{$panelId},id",
            'path' => 'nullable|string|max:255',
            'methods' => 'nullable|array',
            'methods.*' => 'nullable',
            'panel_source' => 'nullable|in:database,array,api',
            'version' => 'nullable|string|max:10',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'description' => 'nullable|string|max:1000',
            'metadata' => 'nullable|array',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateMethods($validator);
        });
    }

    protected function validateMethods($validator): void
    {
        $methods = $this->input('methods');

        if (empty($methods)) {
            return;
        }

        $schema = $this->getSchemaHelper();

        foreach ($methods as $methodName => $parameters) {
            if (!method_exists(Panel::class, $methodName)) {
                $validator->errors()->add("methods.{$methodName}",
                    "Method '{$methodName}' does not exist on Panel class");
                continue;
            }

            $methodConfig = $schema->getMethodConfig($methodName);
            if (!$methodConfig) {
                $validator->errors()->add("methods.{$methodName}",
                    "Method '{$methodName}' not found in panel schema");
                continue;
            }

            $this->validateMethodByType($validator, $methodName, $parameters, $schema);
        }
    }

    protected function validateMethodByType($validator, string $methodName, mixed $parameters, PanelSchemaHelper $schema): void
    {
        if ($schema->isBooleanMethod($methodName)) {
            $this->validateBooleanMethod($validator, $methodName, $parameters);
        } elseif ($schema->isArrayMethod($methodName)) {
            $this->validateArrayMethod($validator, $methodName, $parameters);
        } elseif ($schema->isStringMethod($methodName)) {
            $this->validateStringMethod($validator, $methodName, $parameters);
        }
    }

    protected function validateBooleanMethod($validator, string $methodName, mixed $parameters): void
    {
        if (!is_bool($parameters) && !is_null($parameters)) {
            $validator->errors()->add("methods.{$methodName}",
                "Boolean method '{$methodName}' must have boolean or null parameter");
        }
    }

    protected function validateArrayMethod($validator, string $methodName, mixed $parameters): void
    {
        if (!is_array($parameters) && !is_null($parameters)) {
            $validator->errors()->add("methods.{$methodName}",
                "Array method '{$methodName}' must have array or null parameter");
        }
    }

    protected function validateStringMethod($validator, string $methodName, mixed $parameters): void
    {
        if (!is_string($parameters) && !is_null($parameters)) {
            $validator->errors()->add("methods.{$methodName}",
                "String method '{$methodName}' must have string or null parameter");
        }
    }

    protected function getSchemaHelper(): PanelSchemaHelper
    {
        if (!$this->schemaHelper) {
            $this->schemaHelper = new PanelSchemaHelper();
        }

        return $this->schemaHelper;
    }

    public function messages(): array
    {
        return [
            'id.unique' => 'A panel with this ID already exists',
            'panel_source.in' => 'Panel source must be database, array, or api',
        ];
    }
}
