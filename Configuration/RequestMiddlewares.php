<?php

return [
    'frontend' => [
        'rx/name' => [
            'target' => \ServerKnights\SkNewsletterhelper\Middleware\SkModifyHtmlContent::class,
            'after' => [
                'typo3/cms-frontend/content-length-headers'
            ],
        ],
    ]
];