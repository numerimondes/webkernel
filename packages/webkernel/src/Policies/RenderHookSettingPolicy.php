<?php

namespace Webkernel\Policies;

use Illuminate\Support\Facades\File;
use Webkernel\Models\RenderHookSetting;
use App\Models\User;

class RenderHookSettingPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, RenderHookSetting $renderHookSetting): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, RenderHookSetting $renderHookSetting): bool
    {
        // Si le `view_path` est vide, on autorise l'édition
        // Si le `view_path` est rempli, on autorise l'édition seulement si le fichier de vue réel n'existe pas sur le disque
        return empty($renderHookSetting->view_path) || !File::exists(self::getFullViewPath($renderHookSetting));
    }

    public function delete(User $user, RenderHookSetting $renderHookSetting): bool
    {
        return $user->is_admin;
    }

    public function restore(User $user, RenderHookSetting $renderHookSetting): bool
    {
        return $user->is_admin;
    }

    public function forceDelete(User $user, RenderHookSetting $renderHookSetting): bool
    {
        return $user->is_admin;
    }

    public static function originalViewExists($record): bool
    {
        $viewPath = self::getViewPathFromHookKey($record->hook_key);
        return !empty($viewPath);
    }

    public static function getFullViewPath($record): string
    {
        $viewPath = self::getViewPathFromHookKey($record->hook_key);
        if (empty($viewPath)) {
            return '';
        }
        return resource_path('views/' . str_replace('.', '/', $viewPath) . '.blade.php');
    }

    public static function getViewPathFromHookKey(string $key): string
    {
        $hook = RenderHookSetting::where('hook_key', $key)->first();
        if ($hook) {
            return $hook->view_path;
        }
        return '';
    }
}
