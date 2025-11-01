<?php declare(strict_types=1);

namespace Webkernel\Aptitudes\AccessControl\Traits;

use Illuminate\View\View;

trait HasInlineStatsWidget
{
    protected function getHeaderWidgets(): array
    {
        return [];
    }

    public function getHeader(): ?View
    {
        return view($this->getStatsViewPath(), [
            'stats' => $this->getStatsData()
        ]);
    }

    abstract protected function getStatsViewPath(): string;
    abstract protected function getStatsData(): array;
}
