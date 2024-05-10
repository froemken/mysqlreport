<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'MySQL Report',
    'description' => 'Analyze and profile your TYPO3 database queries',
    'category' => 'module',
    'author' => 'Stefan Froemken',
    'author_email' => 'froemken@gmail.com',
    'state' => 'stable',
    'author_company' => '',
    'version' => '3.0.4',
    'constraints' => [
        'depends' => [
            'typo3' => '13.0.0-13.4.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
