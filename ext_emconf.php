<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'A2A Integration (Agent2Agent Protocol)',
    'description' => 'The site publishes an Agent Card and agents discover it and delegate tasks over JSON-RPC, streaming a task lifecycle with artifacts. Ships a backend A2A Console and a frontend Concierge content element.',
    'category' => 'module',
    'author' => 'webconsulting GmbH',
    'author_email' => 'office@webconsulting.at',
    'author_company' => 'webconsulting GmbH',
    'state' => 'beta',
    'version' => '0.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '14.3.0-14.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
