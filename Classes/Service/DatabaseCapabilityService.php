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
use StefanFroemken\Mysqlreport\Domain\Repository\VariablesRepositoryInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;

final readonly class DatabaseCapabilityService implements DatabaseCapabilityServiceInterface
{
    public function __construct(
        private ConnectionPool $connectionPool,
        private VariablesRepositoryInterface $variablesRepository,
    ) {}

    public function isQueryCacheSupported(?Variables $variables = null): bool
    {
        $vars = $variables ?? $this->variablesRepository->findAll();

        if (isset($vars['have_query_cache'])
            && strtoupper((string)$vars['have_query_cache']) === 'NO'
        ) {
            return false;
        }

        return isset($vars['query_cache_type']);
    }

    public function isQueryCacheEnabled(?Variables $variables = null): bool
    {
        $vars = $variables ?? $this->variablesRepository->findAll();

        if (!$this->isQueryCacheSupported($vars)) {
            return false;
        }

        $type = strtolower((string)($vars['query_cache_type'] ?? 'off'));
        $size = (int)($vars['query_cache_size'] ?? 0);

        return in_array($type, ['on', '1', 'demand', '2'], true) && $size > 0;
    }

    public function getDatabasePlatform(): AbstractPlatform
    {
        return $this->connectionPool
            ->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME)
            ->getDatabasePlatform();
    }
}
