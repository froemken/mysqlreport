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
     * Returns true if Query Cache is activated
     *
     * @api
     */
    public function isQueryCacheEnabled(Variables $variables): bool
    {
        return isset($variables['query_cache_type'])
            && (
                strtolower($variables['query_cache_type']) === 'on'
                || (
                    is_numeric($variables['query_cache_type'])
                    && (int)($variables['query_cache_type']) === 1
                )
            );
    }
}
