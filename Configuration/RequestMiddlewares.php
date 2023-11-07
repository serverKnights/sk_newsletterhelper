<?php

return [
    'frontend' => [
        'rx/name' => [
            'target' => \ServerKnights\SkNewsletterhelper\Middleware\ModifyHtmlContent::class,
            'after' => [
                'typo3/cms-frontend/content-length-headers'
            ],
        ],
    ]
];