<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Logger;

use StefanFroemken\Mysqlreport\Domain\Model\QueryInformation;

interface LoggerInterface
{
    /**
     * This method will be called just after the query has been executed by doctrine.
     * Start collecting duration and other stuff.
     *
     * @param array<int, string> $params
     * @param array<int, string> $types
     */
    public function stopQuery(string $query, float $duration, array $params = [], array $types = []): ?QueryInformation;
}
