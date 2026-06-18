<?php

declare(strict_types=1);

defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

/**
 * Frontend plugin: A2A "Expert Router" (Concierge).
 *
 * A real Extbase plugin (its own CType) that an editor inserts like any plugin.
 * The visitor states a request and the site agent delegates it to the right
 * specialist via the A2A task lifecycle. Reuses the existing eID endpoint + JS + CSS.
 */
$cType = ExtensionUtility::registerPlugin(
    'A2aIntegration',
    'Concierge',
    'A2A: Expert Router',
    'a2a-plugin-concierge',
    'plugins',
    'Reads a visitor request and delegates it to the right specialist agent (A2A), returning a tailored next step.',
);

ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:a2a_integration/Configuration/FlexForms/Concierge.xml',
    $cType,
);

ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--div--;Plugin,pi_flexform',
    $cType,
    'after:palette:headers',
);

$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes'][$cType] = 'a2a-plugin-concierge';
