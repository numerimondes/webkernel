<?php
declare(strict_types=1);

namespace Platform\Numerimondes\Server\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $software_id
 * @property string $name
 * @property string $version
 * @property array $zip_path
 * @property string $install_path
 * @property string $namespace
 * @property string $hash
 * @property string $validation_status
 * @property array $metadata
 * @property \Illuminate\Support\Carbon|null $validated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static> query()
 * @method static \Illuminate\Database\Eloquent\Builder<static> where(string|array|\Closure $column, mixed $operator = null, mixed $value = null)
 * @method static static|null find(int|string $id, array $columns = ['*'])
 * @method static static findOrFail(int|string $id, array $columns = ['*'])
 * @method static static create(array $attributes = [])
 * @method static static firstOrCreate(array $attributes, array $values = [])
 * @method static static updateOrCreate(array $attributes, array $values = [])
 * @method static \Illuminate\Database\Eloquent\Collection<int, static> all(array $columns = ['*'])
 * @method static static|null first()
 * @method static static firstOrFail()
 * @method static \Illuminate\Database\Eloquent\Builder<static> newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static> newQuery()
 * @mixin \Eloquent
 */
class SoftwareCore extends Model
{
  use HasFactory;

  protected $table = 'software_cores';

  protected $fillable = [
    'software_id',
    'name',
    'version',
    'zip_path',
    'install_path',
    'namespace',
    'hash',
    'validation_status',
    'metadata',
    'validated_at',
  ];

  /**
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'zip_path' => 'array',
      'metadata' => 'array',
      'validated_at' => 'datetime',
    ];
  }

  /**
   * Boot method to handle model events
   */
  protected static function boot(): void
  {
    parent::boot();

    static::creating(function ($model) {
      static::calculateHashAndStatus($model);
    });

    static::updating(function ($model) {
      if ($model->isDirty('zip_path')) {
        static::calculateHashAndStatus($model);
      }
    });
  }

  /**
   * Calculate hash and set validation status based on zip file
   */
  protected static function calculateHashAndStatus($model): void
  {
    if ($model->zip_path && !empty($model->zip_path)) {
      $zipPath = is_array($model->zip_path) ? $model->zip_path[0] ?? null : $model->zip_path;

      if ($zipPath) {
        $diskName = config('filesystems.default');
        $disk = Storage::disk($diskName);
        $fullPath = $disk->path($zipPath);

        if (file_exists($fullPath)) {
          $model->hash = hash_file('sha256', $fullPath);
          $model->validation_status = 'validated';
          return;
        }
      }
    }

    if (empty($model->validation_status)) {
      $model->validation_status = 'pending';
    }

    if (empty($model->hash)) {
      $model->hash = '';
    }
  }

  /**
   * Relationship to parent Software
   *
   * @return BelongsTo<Software, $this>
   */
  public function software(): BelongsTo
  {
    return $this->belongsTo(Software::class, 'software_id');
  }

  /**
   * Check if the module has been validated
   */
  public function isValidated(): bool
  {
    return $this->validation_status === 'validated';
  }

  /**
   * Check if validation is in progress
   */
  public function isProcessing(): bool
  {
    return $this->validation_status === 'processing';
  }

  /**
   * Check if validation failed
   */
  public function hasFailed(): bool
  {
    return $this->validation_status === 'failed';
  }
}
