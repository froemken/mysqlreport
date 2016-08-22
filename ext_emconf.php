<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "mysqlreport".
 *
 * Auto generated 22-08-2016 20:44
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
    'title' => 'MySQL Report',
    'description' => 'Analyze and profile your databases queries made via $GLOBALS[\'TYPO3_DB\']',
    'category' => 'misc',
    'author' => 'Stefan Froemken',
    'author_email' => 'froemken@gmail.com',
    'shy' => '',
    'priority' => '',
    'module' => '',
    'state' => 'beta',
    'internal' => '',
    'uploadfolder' => 0,
    'createDirs' => '',
    'modify_tables' => '',
    'clearCacheOnLoad' => 0,
    'lockType' => '',
    'author_company' => '',
    'version' => '0.3.0',
    'constraints' =>array (
        'depends' => array(
            'typo3' => '6.2.0-8.99.99'
        ),
        'conflicts' => array (
        ),
        'suggests' => array (
        ),
    ),
);
