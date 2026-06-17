<?php

declare(strict_types=1);

use Webconsulting\A2aIntegration\Controller\SendController;

/**
 * Backend AJAX route for the A2A Console's Server-Sent-Events stream.
 * Reachable from JS via TYPO3.settings.ajaxUrls['a2a_send'].
 */
return [
    'a2a_send' => [
        'path' => '/a2a/send',
        'target' => SendController::class . '::send',
    ],
];
