<?php

call_user_func(function () {

    /**
     * Table configuration be_users
     */
    $tempColumns = array(
        'tx_groupdelegation_organisationunit' => Array(
            'exclude' => 1,
            #'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:groupdelegation/Resources/Private/Language/locallang.xlf:be_users.tx_groupdelegation_organisationunit',
            'config' => Array(
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_groupdelegation_organisationunit',
                'foreign_table_where' => ' ORDER BY tx_groupdelegation_organisationunit.title',
                'MM' => 'tx_groupdelegation_beusers_organisationunit_mm',
                'size' => '8',
                'multible' => '0',
                'maxitems' => '200',
                'minitems' => '0',
            )
        ),
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('be_users', $tempColumns, 1);
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        'be_users',
        'tx_groupdelegation_organisationunit'
    );
});
