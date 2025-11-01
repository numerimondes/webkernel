<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\AccessControl\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Audit Log Model
 * 
 * Tracks all permission-related changes
 *
 * @property int $id
 * @property string $event_type
 * @property string $auditable_type
 * @property int $auditable_id
 * @property int|null $user_id
 * @property int|null $performed_by
 * @property array<array-key, mixed>|null $old_values
 * @property array<array-key, mixed>|null $new_values
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $reason
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read Model|\Eloquent $auditable
 * @property-read \App\Models\User|null $performedBy
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereAuditableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereAuditableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereEventType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereNewValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereOldValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog wherePerformedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUserId($value)
 * @mixin \Eloquent
 */
class AuditLog extends Model
{
  protected $table = 'users_priv_audit_logs';

  public $timestamps = false;

  protected $fillable = [
    'event_type',
    'auditable_type',
    'auditable_id',
    'user_id',
    'performed_by',
    'old_values',
    'new_values',
    'ip_address',
    'user_agent',
    'reason',
    'created_at',
  ];

  protected $casts = [
    'old_values' => 'array',
    'new_values' => 'array',
    'created_at' => 'datetime',
  ];

  /**
   * Get the auditable model
   */
  public function auditable()
  {
    return $this->morphTo();
  }

  /**
   * Get the user affected by this change
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(config('auth.providers.users.model', \App\Models\User::class), 'user_id');
  }

  /**
   * Get the user who performed this action
   */
  public function performedBy(): BelongsTo
  {
    return $this->belongsTo(config('auth.providers.users.model', \App\Models\User::class), 'performed_by');
  }

  /**
   * Log an event
   */
  public static function log(
    string $eventType,
    Model $auditable,
    ?int $userId = null,
    ?array $oldValues = null,
    ?array $newValues = null,
    ?string $reason = null,
  ): self {
    return self::create([
      'event_type' => $eventType,
      'auditable_type' => get_class($auditable),
      'auditable_id' => $auditable->id,
      'user_id' => $userId,
      'performed_by' => auth()->id(),
      'old_values' => $oldValues,
      'new_values' => $newValues,
      'ip_address' => request()->ip(),
      'user_agent' => request()->userAgent(),
      'reason' => $reason,
      'created_at' => now(),
    ]);
  }
}
