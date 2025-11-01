<?php declare(strict_types=1);

namespace Platform\EnjoyTheWorld\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Platform\EnjoyTheWorld\Models\Provider;
use Platform\EnjoyTheWorld\Models\Service;
use Illuminate\Support\Collection;

/**
 * EnjoyTheWorld User Extension
 *
 * Automatically discovered and merged into User model via MergeUserMethodsFromModules
 * All methods are prefixed with EnjoyTW_ to avoid collisions with other modules.
 *
 * This class provides extension methods and relationships for the User model specific to the EnjoyTheWorld module.
 * It supports dynamic merging without modifying the core User model.
 */
class UserModel
{
  /**
   * @var User The associated User instance.
   */
  protected User $user;

  /**
   * Set the associated User instance.
   * Required for dynamic instantiation in the merging trait.
   *
   * @param User $user The User model instance.
   * @return void
   */
  public function setUser(User $user): void
  {
    $this->user = $user;
  }

  /**
   * Get the provider profile for this user.
   *
   * @return HasOne
   */
  public function EnjoyTW_provider(): HasOne
  {
    return $this->user->hasOne(Provider::class);
  }

  /**
   * Get all services through the provider relationship.
   *
   * @return HasManyThrough
   */
  public function EnjoyTW_services(): HasManyThrough
  {
    return $this->user->hasManyThrough(Service::class, Provider::class, 'user_id', 'provider_id', 'id', 'id');
  }

  /**
   * Check if the user is a registered provider.
   *
   * @return bool
   */
  public function EnjoyTW_isProvider(): bool
  {
    return $this->user->relationLoaded('EnjoyTW_provider')
      ? $this->user->getRelation('EnjoyTW_provider')->exists()
      : $this->user->EnjoyTW_provider()->exists();
  }

  /**
   * Check if the user is an active provider.
   *
   * @return bool
   */
  public function EnjoyTW_isActiveProvider(): bool
  {
    $provider = $this->user->relationLoaded('EnjoyTW_provider')
      ? $this->user->getRelation('EnjoyTW_provider')
      : $this->user->EnjoyTW_provider()->first();

    return $provider && $provider->is_active;
  }

  /**
   * Get the count of active services for this user.
   *
   * @return int
   */
  public function EnjoyTW_activeServicesCount(): int
  {
    if (!$this->EnjoyTW_isProvider()) {
      return 0;
    }

    return $this->user->EnjoyTW_services()->where('is_active', true)->count();
  }

  /**
   * Get the count of featured services for this user.
   *
   * @return int
   */
  public function EnjoyTW_featuredServicesCount(): int
  {
    if (!$this->EnjoyTW_isProvider()) {
      return 0;
    }

    return $this->user->EnjoyTW_services()->where('is_active', true)->where('is_featured', true)->count();
  }

  /**
   * Create or update the provider profile for this user.
   *
   * @param array $data The provider data to create or update.
   * @return Provider
   */
  public function EnjoyTW_createOrUpdateProvider(array $data): Provider
  {
    return $this->user->EnjoyTW_provider()->updateOrCreate(['user_id' => $this->user->id], $data);
  }

  /**
   * Activate the provider profile.
   *
   * @return bool
   */
  public function EnjoyTW_activateProvider(): bool
  {
    $provider = $this->user->relationLoaded('EnjoyTW_provider')
      ? $this->user->getRelation('EnjoyTW_provider')
      : $this->user->EnjoyTW_provider()->first();

    if (!$provider) {
      return false;
    }

    $provider->is_active = true;
    return $provider->save();
  }

  /**
   * Deactivate the provider profile.
   *
   * @return bool
   */
  public function EnjoyTW_deactivateProvider(): bool
  {
    $provider = $this->user->relationLoaded('EnjoyTW_provider')
      ? $this->user->getRelation('EnjoyTW_provider')
      : $this->user->EnjoyTW_provider()->first();

    if (!$provider) {
      return false;
    }

    $provider->is_active = false;
    return $provider->save();
  }

  /**
   * Get all active services for this user.
   *
   * @return Collection
   */
  public function EnjoyTW_getActiveServices(): Collection
  {
    if (!$this->EnjoyTW_isProvider()) {
      return collect();
    }

    return $this->user
      ->EnjoyTW_services()
      ->where('is_active', true)
      ->with(['serviceType', 'translations', 'media'])
      ->get();
  }

  /**
   * Get all featured services for this user.
   *
   * @return Collection
   */
  public function EnjoyTW_getFeaturedServices(): Collection
  {
    if (!$this->EnjoyTW_isProvider()) {
      return collect();
    }

    return $this->user
      ->EnjoyTW_services()
      ->where('is_active', true)
      ->where('is_featured', true)
      ->with(['serviceType', 'translations', 'media'])
      ->get();
  }
}
