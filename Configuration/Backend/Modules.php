<?php

use ElementareTeilchen\Groupdelegation\Controller\BackendController;

/**
 * Definitions for modules provided by EXT:groupdelegation
 */
return [
    'site_groupdelegation' => [
        'parent' => 'site',
        'access' => 'user',
        'iconIdentifier' => 'module-groupdelegation',
        'labels' => 'LLL:EXT:groupdelegation/Resources/Private/Language/locallang.xlf',
        'extensionName' => 'Groupdelegation',
        'controllerActions' => [
            BackendController::class => [
                'index', 'edit', 'save',
            ],
        ],
    ],
];
