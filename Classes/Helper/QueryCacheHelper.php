<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Helper;

use StefanFroemken\Mysqlreport\Domain\Model\Variables;

/**
 * Helper with useful methods for Query Cache
 */
readonly class QueryCacheHelper
{
    /**
     * Returns true if the database engine supports Query Cache.
     * MySQL 8.0+ removed the Query Cache entirely.
     */
    public function isQueryCacheSupported(Variables $variables): bool
    {
        if (isset($variables['have_query_cache'])
            && strtoupper((string)$variables['have_query_cache']) === 'NO'
        ) {
            return false;
        }

        return isset($variables['query_cache_type']);
    }

    /**
     * Returns true if Query Cache is both supported and actively enabled.
     * Note: If query_cache_size is 0, the cache is not active even if type is ON.
     */
    public function isQueryCacheEnabled(Variables $variables): bool
    {
        if (!$this->isQueryCacheSupported($variables)) {
            return false;
        }

        $type = strtolower((string)($variables['query_cache_type'] ?? 'off'));
        $size = (int)($variables['query_cache_size'] ?? 0);

        return in_array($type, ['on', '1', 'demand', '2'], true) && $size > 0;
    }
}
