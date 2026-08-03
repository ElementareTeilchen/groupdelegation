<?php

use ElementareTeilchen\Groupdelegation\Controller\BackendController;
use TYPO3\CMS\Core\Information\Typo3Version;

$typo3Version = (new Typo3Version())->getMajorVersion();

/**
 * Definitions for modules provided by EXT:groupdelegation
 */
return [
    'site_groupdelegation' => [
        'parent' => 'site',
        'access' => 'user',
        'iconIdentifier' => $typo3Version >= 13
            ? 'module-groupdelegation'
            : 'module-groupdelegation-v13',
        'labels' => 'LLL:EXT:groupdelegation/Resources/Private/Language/locallang.xlf',
        'extensionName' => 'Groupdelegation',
        'controllerActions' => [
            BackendController::class => [
                'index', 'edit', 'save',
            ],
        ],
    ],
];
