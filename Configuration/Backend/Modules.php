<?php

declare(strict_types=1);

use ServerKnights\SkNewsletterhelper\Controller\BackendModuleController;

/**
 * Definitions for module
 */
return [
    'newsletterhelper' => [
        'parent' => 'web',
        'position' => 'bottom',
        'access' => 'admin',
        'navigationComponentId' => '',
        'inheritNavigationComponentFromMainModule' => false,
        'labels' => ['title' => 'Newsletter Helper'],
        'extensionName' => 'sk_newsletterhelper',
        'controllerActions' => [
            BackendModuleController::class => [
                'showStartButtons',
            ],
        ],
    ],
];