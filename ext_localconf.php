<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE === 'BE') {

    call_user_func(
        function ($extKey) {

            $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);

            $iconRegistry->registerIcon(
                'extensions-groupdelegation-subadmin-group',
                \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
                ['source' => 'EXT:' . $extKey . '/Resources/Public/Images/sub-admin-group-backend.svg']
            );
        },
        $_EXTKEY
    );
}