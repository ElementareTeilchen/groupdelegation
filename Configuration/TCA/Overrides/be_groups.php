<?php

$tempColumns = array (
    'tx_groupdelegation_delegateable' => Array (
        'exclude' => 1,
        'l10n_mode' => 'exclude',
        'label' => 'LLL:EXT:groupdelegation/locallang_db.xml:be_groups.tx_groupdelegation_delegateable',
        'config' => Array (
            'type' => 'select',
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
        'label' => 'LLL:EXT:groupdelegation/locallang_db.xml:be_groups.tx_groupdelegation_issubadmingroup',
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
        'label' => 'LLL:EXT:groupdelegation/locallang_db.xml:be_groups.tx_groupdelegation_organisationunit',
        'config' => Array (
            'type' => 'select',
            'foreign_table' => 'tx_groupdelegation_organisationunit',
            'foreign_table_where' => ' ORDER BY tx_groupdelegation_organisationunit.title',
            'MM' => 'tx_groupdelegation_begroups_organisationunit_mm',
            'size' => '8',
            'multible' => '0',
            'maxitems' => '200',
            'minitems' => '0',
            'wizards' => Array(
                '_PADDING' => 1,
                '_VERTICAL' => 1,
                'edit' => Array(
                    'type' => 'popup',
                    'title' => 'Edit organisation unit',
                    'script' => 'wizard_edit.php',
                    'icon' => 'edit2.gif',
                    'popup_onlyOpenIfSelected' => 1,
                    'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
                ),
                'add' => Array(
                    'type' => 'script',
                    'title' => 'Create new organisation unit',
                    'icon' => 'add.gif',
                    'params' => Array(
                        'table'=>'tx_groupdelegation_organisationunit',
                        'pid' => '0',
                        'setValue' => 'prepend'
                    ),
                    'script' => 'wizard_add.php',
                ),
            )
        )
    );
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('be_groups',$tempColumns,1);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('be_groups','tx_groupdelegation_issubadmingroup;;;;1-1-1');

$TCA['be_groups']['ctrl']['requestUpdate'] = 'tx_groupdelegation_issubadmingroup';
$TCA['be_groups']['ctrl']['typeicon_column'] = 'tx_groupdelegation_issubadmingroup';
$TCA['be_groups']['ctrl']['typeicons']['1'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('groupdelegation').'icon_be_sub_admin.gif';
$TCA['be_groups']['types']['0']['subtype_value_field']= 'tx_groupdelegation_issubadmingroup';
$TCA['be_groups']['types']['0']['subtypes_addlist']['1'] = 'tx_groupdelegation_delegateable,tx_groupdelegation_organisationunit';
$TCA['be_groups']['types']['1']['subtype_value_field']='tx_groupdelegation_issubadmingroup';
$TCA['be_groups']['types']['1']['subtypes_addlist']['1'] = 'tx_groupdelegation_delegateable,tx_groupdelegation_organisationunit';

