<?php

return [
    "ctrl" => array(
        'title' => 'LLL:EXT:groupdelegation/Resources/Private/Language/locallang.xlf:tx_groupdelegation_organisationunit',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => "ORDER BY crdate",
        'delete' => 'deleted',
        'rootLevel' => '1',
        'enablecolumns' => array(
            'disabled' => 'hidden',
        ),
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('groupdelegation') . 'Resources/Public/Images/icon_tx_groupdelegation_organisationunit.gif',
    ),
    "interface" => array(
        "showRecordFieldList" => "hidden,title"
    ),
    "columns" => array(
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            )
        ),
        'title' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:groupdelegation/Resources/Private/Language/locallang.xlf:tx_groupdelegation_organisationunit.title',
            'config' => array(
                'type' => 'input',
                'size' => '30',
            )
        ),
    ),
    "types" => array(
        "0" => array("showitem" => "hidden;;1;;1-1-1, title;;;;2-2-2")
    ),
    "palettes" => array(
        "1" => array("showitem" => "")
    )
];
