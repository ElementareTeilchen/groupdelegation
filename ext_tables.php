<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

if (TYPO3_MODE == 'BE') {

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'In2code.groupdelegation',
        'web',      // Main area
        'mod1',     // Name of the module
        '',         // Position of the module
        array(      // Allowed controller action combinations
            'Backend' => 'index',
        )
    );
}
?>
