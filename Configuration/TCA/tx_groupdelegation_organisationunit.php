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
        'iconfile' => 'EXT:groupdelegation/Resources/Public/Images/sitemap.svg',
    ),
    "interface" => array(
        "showRecordFieldList" => "hidden,title"
    ),
    "columns" => array(
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
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
        "0" => array("showitem" => "hidden,--palette--;;1,title")
    ),
    "palettes" => array(
        "1" => array("showitem" => "")
    )
];
