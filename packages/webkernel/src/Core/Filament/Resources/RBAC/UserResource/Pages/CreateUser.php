<?php

namespace Webkernel\Core\Filament\Resources\RBAC\UserResource\Pages;

use Webkernel\Core\Filament\Resources\RBAC\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public bool $isCreating = false;

    public function mount(): void
    {
        parent::mount();
        $this->isCreating = true; // Nous sommes en mode création
    }

    public function getIsCreating(): bool
    {
        return $this->isCreating;
    }
}
