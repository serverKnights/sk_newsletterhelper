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
        'access' => 'user',
        'navigationComponentId' => 'web',
        'labels' => ['title' => 'Newsletter Helper'],
        'extensionName' => 'sk_newsletterhelper',
        'controllerActions' => [
            BackendModuleController::class => [
                'showStartButtons',
            ],
        ],
        'moduleData' => [
            'language' => 0,
        ],
    ],
];