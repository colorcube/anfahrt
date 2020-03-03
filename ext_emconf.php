<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Anfahrt: Simple map for one location',
    'description' => 'Simple map plugin with marker and popup window. Comfortable geo coding in backend. Uses Google Maps.',
    'category' => 'plugin',
    'author' => 'René Fritz',
    'author_email' => 'r.fritz@colorcube.de',
    'author_company' => 'Colorcube',
    'version' => '1.1.2',
    'state' => 'stable',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-9.5.999'
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'Colorcube\\Anfahrt\\' => 'Classes'
        ]
    ]
];
