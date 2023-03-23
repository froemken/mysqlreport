<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'MySQL Report',
    'description' => 'Analyze and profile your TYPO3 databases queries',
    'category' => 'module',
    'author' => 'Stefan Froemken',
    'author_email' => 'froemken@gmail.com',
    'state' => 'stable',
    'author_company' => '',
    'version' => '2.0.3',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.23-12.5.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
