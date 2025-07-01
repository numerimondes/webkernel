<?php
// platform/Platform.php

namespace WebkernelSubPlatform;

class Platform
{
    public function initialize()
    {
        setCorePlatformInfos([
            'brandName' => 'REAM',
            'version' => '0.1.0',
            'description' => 'Gestion des audits d’énergies renouvelables',
            'logoLink' => 'packages/webkernel/src/resources/repo-assets/credits/ream.svg',
        ], 10);
    }
}
