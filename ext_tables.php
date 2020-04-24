<?php
declare(strict_types=1);
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}
// @extensionScannerIgnoreLine
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'groupdelegation',
        'user',
        'mod1',
        '',
        [
            In2code\Groupdelegation\Controller\BackendController::class => 'index, edit, save',
        ],
        [
            'access' => 'user, group',
            'icon' => 'EXT:groupdelegation/Resources/Public/Images/moduleicon.svg',
            'labels' => 'LLL:EXT:groupdelegation/Resources/Private/Language/locallang.xlf'
        ]
    );
