<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Service;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use StefanFroemken\Mysqlreport\Domain\Model\Variables;

interface DatabaseCapabilityServiceInterface
{
    public function isQueryCacheSupported(?Variables $variables = null): bool;

    public function isQueryCacheEnabled(?Variables $variables = null): bool;

    public function getDatabasePlatform(): AbstractPlatform;
}
