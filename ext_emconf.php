<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'MySQL Report',
    'description' => 'Analyze and profile your databases queries made via $GLOBALS[\'TYPO3_DB\']',
    'category' => 'module',
    'author' => 'Stefan Froemken',
    'author_email' => 'froemken@gmail.com',
    'state' => 'beta',
    'author_company' => '',
    'version' => '0.3.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.34-9.99.99'
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
