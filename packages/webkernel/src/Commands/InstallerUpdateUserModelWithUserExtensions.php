<?php

namespace Webkernel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallerUpdateUserModelWithUserExtensions extends Command
{
    protected $signature = 'webkernel:install-update-user-model';
    protected $description = 'Webkernel Update User Model with Adding UserExtensions (Trait)';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Call the method to add the UserExtensions trait
        $this->putUserExtensionsTraitInUserModel();
    }

    protected function putUserExtensionsTraitInUserModel(): void
    {
        $filePath = base_path('app/Models/User.php');
        $traitDeclaration = "use Webkernel\Traits\UserExtensions;\n";
        $classDeclaration = "class User extends Authenticatable\n";

        if (!File::exists($filePath)) {
            $this->error('❌ app/Models/User.php not found.');
            exit(1);
        }

        $contents = File::get($filePath);

        // Check if the trait is already added, if not add it
        if (strpos($contents, $traitDeclaration) === false) {
            $contents = preg_replace("/$classDeclaration/", "$classDeclaration\n    $traitDeclaration", $contents);
            File::put($filePath, $contents);
            $this->info('[✓] Added UserExtensions trait to User model');
        } else {
            $this->info('[✓] UserExtensions trait already present in User model');
        }
    }
}
