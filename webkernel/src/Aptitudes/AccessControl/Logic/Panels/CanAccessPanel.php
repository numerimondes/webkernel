<?php declare(strict_types=1);

namespace Webkernel\Aptitudes\AccessControl\Logic\Panels;

use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CanAccessPanel
{
  private const DEBUG = true;

  /**
   * @param Panel $panel
   * @param User|null $user
   * @return bool
   */
  public static function canAccessPanel(Panel $panel, ?User $user = null): bool
  {
    $targetUser = $user ?? Auth::user();

    if ($targetUser === null) {
      if (self::DEBUG) {
        Log::warning('[CanAccessPanel] No authenticated user', [
          'panel_id' => $panel->getId(),
        ]);
      }
      return false;
    }

    if (!($targetUser instanceof User)) {
      if (self::DEBUG) {
        Log::warning('[CanAccessPanel] Invalid user instance', [
          'panel_id' => $panel->getId(),
          'user_class' => get_class($targetUser),
        ]);
      }
      return false;
    }

    $userId = (int) $targetUser->id;
    $panelId = $panel->getId();

    $hasAccess = self::checkAccess($userId, $panelId);

    if (self::DEBUG) {
      Log::info('[CanAccessPanel] Access check result', [
        'panel_id' => $panelId,
        'user_id' => $userId,
        'has_access' => $hasAccess,
      ]);
    }

    /**
     *       ⢀⣀⣀⡀                    ⠸⣧      ⢀⣿
     *    ⢀⣴⣿⣿⣿⣿⣿⣿⣦⡀     ⣀⡀           ⢿⡇ ⣠⣄  ⣼⡇
     *   ⣰⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣦⡀  ⠉⠛⠛          ⠸⡟ ⣿⣿⡇⢰⣿
     *  ⢰⣿⣿⣿⣿⣿⣿⣿⣿⣿⡿⠿⠿⣷⡄ ⣀⣀⠓           ⣿⡀⢿⡿⠃⣸⡏
     *  ⣿⣿⣿⣿⣿⣿⣿⣿⡿⠋⣠⣶⣦⣈⠻⣦⠈⠋            ⢹⣿⣦⣤⣾⣿⠇
     *  ⢻⣿⣿⣿⣿⣿⣿⡿⠁⣼⡿⠻⣿⣿⣷⣄⣠⣴⠆           ⢸⣿⣿⣿⣿⣿
     *   ⠻⣿⣿⣿⣿⡟ ⢸⣿⣿⣦⣄⡉⠛⠛⠛⠋            ⠈⣿⣿⣿⣿⡟
     *    ⠈⠛⠿⠋  ⢻⣿⣿⣿⣿⣿⣧⡀              ⢀⣿⣿⣿⣿⣷
     *   ⣀⣀⣤⣶⣄  ⠈⠿⢿⣿⣿⣿⣿⣿⣶⣤⡀           ⢸⣿⠇ ⣿⣿
     *  ⠸⠟⠛⠉⠻⣿⣧⡀⣼⣶⣤⣄⠉⠉⠛⠛⠻⢿⣿⣦⡀   ⣠⡀    ⣼⣿  ⢻⣿⡇
     *       ⠙⢿⣿⣿⡿⠋⠁      ⠙⢿⣿⣆⣠⣾⠟⠁    ⣿⡏  ⢸⣿⣇
     *         ⠈⠁           ⠻⣿⡿⠁     ⢠⣿⠇   ⣿⣿
     *                               ⢸⣿    ⢹⣿⡀
     *                               ⠈⠉    ⠈⠉⠁
     * Only Authorized users can access the panels !
     */

    return $hasAccess;
  }

  /**
   * @param int $userId
   * @param string $panelId
   * @return bool
   */
  private static function checkAccess(int $userId, string $panelId): bool
  {
    $exists = DB::table('users_priv_panel_access')
      ->where('user_id', '=', $userId)
      ->where('panel_id', '=', $panelId)
      ->where('is_active', '=', true)
      ->exists();

    if (self::DEBUG) {
      Log::debug('[CanAccessPanel] DB query result', [
        'user_id' => $userId,
        'panel_id' => $panelId,
        'exists' => $exists,
      ]);
    }

    return $exists;
  }

  /**
   * @return array<string, Panel>
   */
  public static function getAvailablePanels(): array
  {
    return Filament::getPanels();
  }

  /**
   * @param array<int, Panel> $panels
   * @param User|null $user
   * @return array<string, bool>
   */
  public static function canAccessMultiplePanels(array $panels, ?User $user = null): array
  {
    $results = [];
    foreach ($panels as $panel) {
      $results[$panel->getId()] = self::canAccessPanel($panel, $user);
    }
    return $results;
  }

  /**
   * @param User|null $user
   * @return array<string, Panel>
   */
  public static function getAccessiblePanels(?User $user = null): array
  {
    $allPanels = self::getAvailablePanels();
    $accessiblePanels = [];

    foreach ($allPanels as $panelId => $panel) {
      if (self::canAccessPanel($panel, $user)) {
        $accessiblePanels[$panelId] = $panel;
      }
    }

    return $accessiblePanels;
  }
}
