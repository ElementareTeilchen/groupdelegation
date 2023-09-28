<?php

// file is added for publishing extension to TER using typo3/tailor

$EM_CONF[$_EXTKEY] = [
    'title' => 'Groupdelegation',
    'description' => 'Allows generating Sub-Admin be_user by groups which can handle access of other be_user by groups',
    'category' => 'module',
    'author' => 'Sebastian Mueller, Marcus Schwemer, Andreas Sommer, Franz Kugelmann',
    'state' => 'stable',
    'version' => '5.0.3',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.9.99',
            'php' => '7.4.0-8.2.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];