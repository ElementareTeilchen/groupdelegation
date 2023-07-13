<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:groupdelegation/Resources/Private/Language/locallang.xlf:tx_groupdelegation_organisationunit',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'default_sortby' => 'ORDER BY crdate',
        'delete' => 'deleted',
        'rootLevel' => '1',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'iconfile' => 'EXT:groupdelegation/Resources/Public/Images/sitemap.svg',
    ],
    'columns' => [
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => '0'
            ]
        ],
        'title' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:groupdelegation/Resources/Private/Language/locallang.xlf:tx_groupdelegation_organisationunit.title',
            'config' => [
                'type' => 'input',
                'size' => '30',
            ]
        ],
    ],
    'types' => [
        '0' => ['showitem' => 'hidden,--palette--;;1,title']
    ],
    'palettes' => [
        '1' => ['showitem' => '']
    ]
];
