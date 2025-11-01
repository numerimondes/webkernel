<?php
declare(strict_types=1);
namespace Webkernel\Aptitudes\Users\Filament\Resources\Users\Pages;

use Webkernel\Aptitudes\Users\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
