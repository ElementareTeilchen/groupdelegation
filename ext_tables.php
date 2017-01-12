<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

if (TYPO3_MODE == 'BE') {

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'In2code.groupdelegation',
        'user',      // Main area
        'mod1',     // Name of the module
        '',         // Position of the module
        array(      // Allowed controller action combinations
            'Backend' => 'index',
        ),
        array(
            'access' => 'user, group',
            'icon' => 'EXT:groupdelegation/Resources/Public/Icons/moduleicon.svg',
            'labels' => 'LLL:EXT:groupdelegation/Resources/Private/Language/locallang.xlf'
        )
    );
}
?>
