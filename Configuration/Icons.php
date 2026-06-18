<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'a2a-module' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:a2a_integration/Resources/Public/Icons/module-a2a.svg',
    ],
    'a2a-plugin-concierge' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:a2a_integration/Resources/Public/Icons/plugin-concierge.svg',
    ],
];
