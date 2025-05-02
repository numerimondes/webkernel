<?php

namespace Webkernel\Models;

use Illuminate\Database\Eloquent\Model;

class RenderHookSetting extends Model
{
    protected $table = 'render_hook_settings';

    protected $fillable = [
        'hook_key',
        'icon',
        'where_placed',
        'scopes',
        'translation_desc_key',
        'view_path',
        'enabled',
        'customizable',
        'customization_rel_ink'
    ];

    public $timestamps = true;

    protected $casts = [
        'scopes' => 'array',
    ];

    public function scopeByHookKey($query, $hookKey)
    {
        return $query->where('hook_key', $hookKey);
    }

    public static function getViewPathFromHookKey(string $key): string
    {
        $hook = self::where('hook_key', $key)->first();

        // If the hook exists, return the view_path from the database
        if ($hook) {
            return $hook->view_path;
        }

        // Return empty string as fallback
        return '';
    }
}
