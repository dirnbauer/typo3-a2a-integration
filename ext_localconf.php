<?php

declare(strict_types=1);

defined('TYPO3') or die();

use TYPO3\CMS\Core\Cache\Backend\FileBackend;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use Webconsulting\A2aIntegration\Eid\AgentCardEndpoint;
use Webconsulting\A2aIntegration\Eid\ConciergeEndpoint;
use Webconsulting\A2aIntegration\Eid\RpcEndpoint;

// The site as an A2A *server*: a discoverable Agent Card + a JSON-RPC endpoint.
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['a2a_card'] = AgentCardEndpoint::class . '::card';
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['a2a_rpc'] = RpcEndpoint::class . '::rpc';

// Frontend Concierge content element: delegates a task and streams its lifecycle.
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['a2a_concierge'] = ConciergeEndpoint::class . '::run';

// Lightweight cache for rate limiting + task bookkeeping.
$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['a2a'] ??= [
    'frontend' => VariableFrontend::class,
    'backend' => FileBackend::class,
    'groups' => ['system'],
];
