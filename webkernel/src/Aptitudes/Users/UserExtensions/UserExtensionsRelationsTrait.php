<?php
namespace Webkernel\Aptitudes\Users\UserExtensions;

use Webkernel\Aptitudes\Users\UserExtensions\ExtensionManager;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Webkernel\Aptitudes\RBAC\Traits\PanelAccessTrait;
use Webkernel\Aptitudes\Users\Models\UserExtensions\UserPreference;

trait UserExtensionsRelationsTrait
{

    /**
     * Cached extension manager instance to avoid recreating on multiple calls.
     *
     * @var ExtensionManager|null
     */
    protected ?ExtensionManager $extensionManagerCache = null;

    /**
     * Get the user preferences.
     */
    public function preferences(): HasOne
    {
        return $this->hasOne(UserPreference::class, 'user_id');
    }

    /**
     * Get the extension manager instance for this user model.
     *
     * @return ExtensionManager
     */
    public function extension(): ExtensionManager
    {
        if ($this->extensionManagerCache === null) {
            $this->extensionManagerCache = new ExtensionManager($this);
        }

        return $this->extensionManagerCache;
    }

    /**
     * Create a HasOne relationship to the specified extension model class.
     *
     * @param class-string $class The fully qualified extension model class name
     * @return HasOne<\Illuminate\Database\Eloquent\Model>
     */
    public function extensionRelation(string $class): HasOne
    {
        return $this->hasOne($class, 'user_id', 'id');
    }

    /**
     * Handle dynamic property access for extension models.
     * Attempts to resolve extension relationships through the extension manager
     * before falling back to default Eloquent behavior.
     *
     * @param string $key The property name being accessed
     * @return mixed
     */
    public function __get($key)
    {
        /**
         * First attempt to resolve the property as an extension relationship.
         * This allows accessing extension models through intuitive property names
         * regardless of their namespace location.
         */
        $extensionManager = $this->extension();
        $extensionClass = $extensionManager->findExtensionByRelationshipName($key);

        if ($extensionClass !== null) {
            /**
             * Check if the relationship is already loaded to avoid redundant queries.
             * This optimization is particularly important when accessing extension
             * properties multiple times within the same request cycle.
             */
            $relationshipKey = $this->getRelationshipKeyForExtension($extensionClass);

            if ($this->relationLoaded($relationshipKey)) {
                return $this->getRelation($relationshipKey);
            }

            $result = $this->extensionRelation($extensionClass)->first();
            $this->setRelation($relationshipKey, $result);

            return $result;
        }

        return parent::__get($key);
    }

    /**
     * Generate a consistent relationship key for caching extension relationships.
     * This ensures that multiple accesses to the same extension use cached data.
     *
     * @param class-string $extensionClass The extension model class name
     * @return string
     */
    protected function getRelationshipKeyForExtension(string $extensionClass): string
    {
        return 'extension_' . md5($extensionClass);
    }

    /**
     * Clear the extension manager cache when the model is refreshed.
     * This ensures that extension mappings remain accurate after model state changes.
     *
     * @param mixed $with
     * @return static
     */
    public function refresh($with = [])
    {
        $this->extensionManagerCache = null;

        return parent::refresh();
    }
}
