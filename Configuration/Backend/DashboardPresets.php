<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

return [
    'mysqlreport' => [
        'title' => 'LLL:EXT:mysqlreport/Resources/Private/Language/locallang.xlf:dashboard.mysqlreport.title',
        'description' => 'LLL:EXT:mysqlreport/Resources/Private/Language/locallang.xlf:dashboard.mysqlreport.description',
        'iconIdentifier' => 'ext-mysqlreport',
        'defaultWidgets' => [
            'mysql-max-used-connections',
            'mysql-inno-db-buffer',
            'mysql-query-types',
            'mysql-created-temp-tables',
            'mysql-handler-read-next',
        ],
        'showInWizard' => true,
    ],
];
