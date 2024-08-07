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
use StefanFroemken\Mysqlreport\Configuration\ExtConf;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Middleware\UsableForConnectionInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * With new driverMiddlewares-hook of TYPO3 we register a Middleware into Doctrine.
 */
readonly class LoggerWithQueryTimeMiddleware implements UsableForConnectionInterface
{
    /**
     * @param array<string, string> $connectionParams
     */
    public function canBeUsedForConnection(string $identifier, array $connectionParams): bool
    {
        if ($identifier !== ConnectionPool::DEFAULT_CONNECTION_NAME) {
            return false;
        }

        if (in_array($connectionParams['driver'] ?? '', ['mysqli', 'pdo_mysql'], true)) {
            return false;
        }

        return GeneralUtility::makeInstance(ExtConf::class)->isQueryLoggingActivated();
    }

    public function wrap(Driver $driver): Driver
    {
        // As $driver is transferred as constructor argument, DI can not be used in that class
        return GeneralUtility::makeInstance(LoggerWithQueryTimeDriver::class, $driver);
    }
}
