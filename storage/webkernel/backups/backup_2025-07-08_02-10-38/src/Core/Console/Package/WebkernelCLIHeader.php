<?php

namespace Webkernel\Core\Console\Package;

use Illuminate\Console\Command;

class WebkernelCLIHeader extends Command
{
    protected $signature = 'webkernel:show-ascii-header';
    protected $description = 'Show the Webkernel ASCII header';
    protected $hidden = true;

    public function handle(): void
    {
        $this->displayWelcomeBanner();
    }

    /**
     * Display the welcome ASCII banner.
     */
    protected function displayWelcomeBanner(): void
    {
        $this->newLine();
        $this->line(" __      __      ___.    ____  __.                         .__   ");
        $this->line("/  \\    /  \\ ____\\_ |__ |    |/ _|___________  ____   ____ |  |  ");
        $this->line("\\   \\/\\/   // __ \\| __ \\|      <_/ __ \\_  __ \\/    \\_/ __ \\|  |  ");
        $this->line(" \\        /\\  ___/| \\_\\ \\    |  \\  ___/|  | \\/   |  \\  ___/|  |__");
        $this->line("  \\__/\\  /  \\___  >___  /____|__ \\___  >__|  |___|  /\\___  >____/");
        $this->line("       \\/       \\/    \\/        \\/   \\/           \\/     \\/      ");
        $this->newLine();

        $this->line('By <href=https://www.numerimondes.com>Numerimondes</>');
    }
}
