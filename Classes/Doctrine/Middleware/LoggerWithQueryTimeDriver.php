<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Doctrine\Middleware;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Middleware\AbstractDriverMiddleware;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * We need our own driver to implement our own connection where we can start using
 * our logger.
 */
final class LoggerWithQueryTimeDriver extends AbstractDriverMiddleware
{
    public function connect(#[\SensitiveParameter] array $params): Connection
    {
        // As parent::connect is transferred as constructor argument, DI can not be used in that class
        return GeneralUtility::makeInstance(LoggerWithQueryTimeConnection::class, parent::connect($params));
    }
}
