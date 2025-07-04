<?php

if (!function_exists('getWebkernelUltimateBrandingConfig')) {
    function getWebkernelUltimateBrandingConfig(): array
    {
        return [
            'webkernel' => [
                'app_location' => 'Webkernel\\',
                'detection' => [
                    'class_exists' => ['Webkernel\\Application'],
                    'file_exists' => [],
                    'constant_defined' => ['Application::WEBKERNEL_VERSION'],
                    'default_active' => true,
                ],
                'version_detection' => [
                    'constant' => 'Application::WEBKERNEL_VERSION',
                    'fallback' => '1.0.0',
                ],
                'subplatforms' => [
                    'student_flow' => [
                        'app_location' => 'StudentFlow\\',
                        'detection' => [
                            'class_exists' => ['StudentFlow\\StudentFlowServiceProvider'],
                            'file_exists' => [],
                            'constant_defined' => ['Application::STUDENT_FLOW_VERSION'],
                            'default_active' => false,
                        ],
                        'version_detection' => [
                            'constant' => 'Application::STUDENT_FLOW_VERSION',
                            'fallback' => '0.9.0',
                        ],
                        'official_subplatform' => true,
                        'versions' => [
                            [
                                'operator' => '<',
                                'version' => '1.0.0',
                                'config' => [
                                    'name' => 'platform_student_flow_name',
                                    'brandName' => 'platform_student_flow_brand_name',
                                    'description' => 'platform_student_flow_description',
                                    'cssTitle' => 'student-flow-style',
                                    'logos' => [
                                        'default_logo' => '/logos/student-flow-default.png',
                                        'light_logo' => '/logos/student-flow-light.png',
                                        'dark_logo' => '/logos/student-flow-dark.png',
                                        'alt' => 'platform_student_flow_logo_alt',
                                    ],
                                    'codename' => 'platform_student_flow_codename',
                                    'series' => '24.12',
                                    'checksum' => 'sha256-studentflow-v0',
                                    'metadata' => [
                                        'title' => 'platform_student_flow_meta_title',
                                        'description' => 'platform_student_flow_meta_description',
                                        'keywords' => 'platform_student_flow_meta_keywords',
                                        'author' => 'El Moumen Yassine',
                                        'robots' => 'index, follow',
                                    ],
                                    'opengraph' => [
                                        'title' => 'platform_student_flow_og_title',
                                        'description' => 'platform_student_flow_og_description',
                                        'image' => '/logos/student-flow-og.png',
                                        'type' => 'website',
                                        'url' => '',
                                        'site_name' => 'platform_student_flow_og_site_name',
                                    ],
                                    'to_change_in_system_panel' => [
                                        'name' => true,
                                        'brandName' => true,
                                        'description' => true,
                                        'logos' => false,
                                        'cssTitle' => false,
                                        'codename' => false,
                                        'series' => false,
                                        'checksum' => false,
                                        'metadata' => true,
                                        'opengraph' => true,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'ream' => [
                        'app_location' => 'Numerimondes\\Modules\\ReamMar\\',
                        'detection' => [
                            'class_exists' => ['Numerimondes\\Modules\\ReamMar\\Providers\\Filament\\MarPanelProvider'],
                            'file_exists' => [
                                'platform/Modules/ReamMar/src/Providers/Filament/MarPanelProvider.php',
                                'platform/Platform.php'
                            ],
                            'constant_defined' => ['Application::REAM_VERSION'],
                            'default_active' => false,
                        ],
                        'version_detection' => [
                            'constant' => 'Application::REAM_VERSION',
                            'fallback' => '0.1.0',
                        ],
                        'official_subplatform' => true,
                        'versions' => [
                            [
                                'operator' => '<',
                                'version' => '1.0.0',
                                'config' => [
                                    'name' => 'REAM',
                                    'brandName' => 'platform_ream_brand_name',
                                    'description' => 'platform_ream_description',
                                    'cssTitle' => 'ream-style',
                                    'logos' => [
                                        'default_logo' => '/packages/webkernel/src/Resources/repo-assets/credits/ream.svg',
                                        'light_logo' => '/packages/webkernel/src/Resources/repo-assets/credits/ream.svg',
                                        'dark_logo' => '/packages/webkernel/src/Resources/repo-assets/credits/ream.svg',
                                        'alt' => 'platform_ream_logo_alt',
                                    ],
                                    'codename' => 'platform_ream_codename',
                                    'series' => '24.12',
                                    'checksum' => 'sha256-ream-v0',
                                    'metadata' => [
                                        'title' => 'platform_ream_meta_title',
                                        'description' => 'platform_ream_meta_description',
                                        'keywords' => 'platform_ream_meta_keywords',
                                        'author' => 'El Moumen Yassine',
                                        'robots' => 'index, follow',
                                    ],
                                    'opengraph' => [
                                        'title' => 'platform_ream_og_title',
                                        'description' => 'platform_ream_og_description',
                                        'image' => '/packages/webkernel/src/Resources/repo-assets/credits/ream.svg',
                                        'type' => 'website',
                                        'url' => '',
                                        'site_name' => 'platform_ream_og_site_name',
                                    ],
                                    'to_change_in_system_panel' => [
                                        'name' => true,
                                        'brandName' => true,
                                        'description' => true,
                                        'logos' => false,
                                        'cssTitle' => false,
                                        'codename' => false,
                                        'series' => false,
                                        'checksum' => false,
                                        'metadata' => true,
                                        'opengraph' => true,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'versions' => [
                    [
                        'operator' => '<',
                        'version' => '1.0.0',
                        'config' => [
                            'name' => 'platform_webkernel_name',
                            'brandName' => 'platform_webkernel_brand_name',
                            'description' => 'platform_webkernel_description',
                            'cssTitle' => 'webkernel-style',
                            'logos' => [
                                'default_logo' => '/logos/webkernel-default.png',
                                'light_logo' => '/logos/webkernel-light.png',
                                'dark_logo' => '/logos/webkernel-dark.png',
                                'alt' => 'platform_webkernel_logo_alt',
                            ],
                            'codename' => 'platform_webkernel_codename',
                            'series' => '24.12',
                            'checksum' => 'sha256-webkernel-v0',
                            'metadata' => [
                                'title' => 'platform_webkernel_meta_title',
                                'description' => 'platform_webkernel_meta_description',
                                'keywords' => 'platform_webkernel_meta_keywords',
                                'author' => 'El Moumen Yassine',
                                'robots' => 'index, follow',
                            ],
                            'opengraph' => [
                                'title' => 'platform_webkernel_og_title',
                                'description' => 'platform_webkernel_og_description',
                                'image' => '/logos/webkernel-og.png',
                                'type' => 'website',
                                'url' => '',
                                'site_name' => 'platform_webkernel_og_site_name',
                            ],
                            'to_change_in_system_panel' => [
                                'name' => true,
                                'brandName' => true,
                                'description' => true,
                                'logos' => false,
                                'cssTitle' => false,
                                'codename' => false,
                                'series' => false,
                                'checksum' => false,
                                'metadata' => true,
                                'opengraph' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}