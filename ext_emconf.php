<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'MySQL Report',
    'description' => 'Analyze and profile your TYPO3 databases queries',
    'category' => 'module',
    'author' => 'Stefan Froemken',
    'author_email' => 'froemken@gmail.com',
    'state' => 'beta',
    'author_company' => '',
    'version' => '0.5.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.32-11.5.16'
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
