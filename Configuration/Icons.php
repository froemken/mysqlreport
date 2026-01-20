<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'ext-mysqlreport' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:mysqlreport/Resources/Public/Icons/Extension.svg',
    ],
    'ext-mysqlreport-module' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:mysqlreport/Resources/Public/Icons/Module.svg',
    ],
];
