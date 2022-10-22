<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Helper;

use StefanFroemken\Mysqlreport\Menu\Page;

/**
 * Helper with useful methods for Query Cache
 */
class QueryCacheHelper
{
    /**
     * Returns true, if Query Cache is activated
     *
     * @api
     */
    public function isQueryCacheEnabled(Page $page): bool
    {
        return isset($page->getVariables()['query_cache_type'])
            && (
                strtolower($page->getVariables()['query_cache_type']) === 'on'
                || (
                    is_numeric($page->getVariables()['query_cache_type'])
                    && (int)($page->getVariables()['query_cache_type']) === 1
                )
            );
    }
}
