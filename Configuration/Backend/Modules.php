<?php

declare(strict_types=1);

use Webconsulting\A2aIntegration\Controller\A2aController;

return [
    'web_a2a' => [
        'parent' => 'web',
        'position' => ['after' => 'web_info'],
        'access' => 'user',
        'workspaces' => 'live',
        'path' => '/module/web/a2a',
        'labels' => 'LLL:EXT:a2a_integration/Resources/Private/Language/locallang_mod.xlf',
        'extensionName' => 'A2aIntegration',
        'iconIdentifier' => 'a2a-module',
        'controllerActions' => [
            A2aController::class => [
                'console',
                'catalog',
            ],
        ],
    ],
];
