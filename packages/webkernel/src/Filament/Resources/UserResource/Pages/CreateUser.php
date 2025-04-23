<?php

namespace Webkernel\Filament\Resources\UserResource\Pages;

use Webkernel\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected bool $isCreating = false;

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
