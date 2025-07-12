<?php

namespace Webkernel\Core\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class Documentation extends Page
{
    protected string $view = 'webkernel::filament.pages.documentation';

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return 'heroicon-o-folder-open';
    }

}
