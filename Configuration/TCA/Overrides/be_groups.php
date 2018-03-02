<?php

$tempColumns = array (
    'tx_groupdelegation_delegateable' => Array (
        'exclude' => 1,
        'l10n_mode' => 'exclude',
        'label' => 'LLL:EXT:groupdelegation/Resources/Private/Language/locallang.xlf:be_groups.tx_groupdelegation_delegateable',
        'config' => Array (
            'type' => 'select',
            'renderType' => 'selectMultipleSideBySide',
            'foreign_table' => 'be_groups',
            'foreign_table_where' => ' ORDER BY be_groups.title',
            'MM' => 'tx_groupdelegation_subadmin_begroups_mm',
            'size' => '8',
            'multiple' => '0',
            'autoSizeMax' => 10,
            'maxitems' => '200',
            'minitems' => '1',
        )
    ),
    'tx_groupdelegation_issubadmingroup' => array (
        'exclude' => 1,
        'label' => 'LLL:EXT:groupdelegation/Resources/Private/Language/locallang.xlf:be_groups.tx_groupdelegation_issubadmingroup',
        'config' => array (
            'type' => 'check',
        )
    ),
    'tx_groupdelegation_canactivate' => array (
        'exclude' => 1,
        'label' => 'LLL:EXT:groupdelegation/Resources/Private/Language/locallang.xlf:be_groups.tx_groupdelegation_canactivate',
        'config' => array (
            'type' => 'check',
        )
    ),
);

$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['groupdelegation']);
if(isset($extConf['ignoreOrganisationUnit']) && $extConf['ignoreOrganisationUnit']==0) {
    $tempColumns['tx_groupdelegation_organisationunit'] = Array (
        'exclude' => 1,
        'l10n_mode' => 'exclude',
        'label' => 'LLL:EXT:groupdelegation/Resources/Private/Language/locallang.xlf:be_groups.tx_groupdelegation_organisationunit',
        'config' => Array (
            'type' => 'select',
            'foreign_table' => 'tx_groupdelegation_organisationunit',
            'foreign_table_where' => ' ORDER BY tx_groupdelegation_organisationunit.title',
            'MM' => 'tx_groupdelegation_begroups_organisationunit_mm',
            'size' => '8',
            'multible' => '0',
            'maxitems' => '200',
            'minitems' => '0',
        )
    );
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('be_groups', $tempColumns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('be_groups', 'tx_groupdelegation_issubadmingroup');

$GLOBALS['TCA']['be_groups']['ctrl']['requestUpdate'] = 'tx_groupdelegation_issubadmingroup';
$GLOBALS['TCA']['be_groups']['ctrl']['typeicon_column'] = 'tx_groupdelegation_issubadmingroup';
$GLOBALS['TCA']['be_groups']['ctrl']['typeicon_classes']['1'] = 'extensions-groupdelegation-subadmin-group';

$GLOBALS['TCA']['be_groups']['types']['0']['subtype_value_field']= 'tx_groupdelegation_issubadmingroup';
$GLOBALS['TCA']['be_groups']['types']['0']['subtypes_addlist']['1'] = 'tx_groupdelegation_canactivate,tx_groupdelegation_delegateable,tx_groupdelegation_organisationunit';
$GLOBALS['TCA']['be_groups']['types']['1']['subtype_value_field']='tx_groupdelegation_issubadmingroup';
$GLOBALS['TCA']['be_groups']['types']['1']['subtypes_addlist']['1'] = 'tx_groupdelegation_canactivate,tx_groupdelegation_delegateable,tx_groupdelegation_organisationunit';

