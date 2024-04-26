<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Doctrine\Middleware;

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Middleware;

/**
 * With new driverMiddlewares-hook of TYPO3 we register a Middleware into Doctrine.
 */
readonly class LoggerWithQueryTimeMiddleware implements Middleware
{
    public function wrap(Driver $driver): Driver
    {
        // Can not use DI here, because we have to transfer an individual driver object
        // to our LoggerWithQueryTimeDriver as constructor argument.
        return new LoggerWithQueryTimeDriver($driver);
    }
}
