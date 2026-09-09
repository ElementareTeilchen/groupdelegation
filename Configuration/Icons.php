<?php

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'module-groupdelegation' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:groupdelegation/Resources/Public/Images/moduleicon.svg',
    ],
    'extensions-groupdelegation-subadmin-group' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:groupdelegation/Resources/Public/Images/sub-admin-group-backend.svg',
    ]
];
