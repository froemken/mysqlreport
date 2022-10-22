<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\QueryCache;

use StefanFroemken\Mysqlreport\Enumeration\StateEnumeration;
use StefanFroemken\Mysqlreport\Helper\QueryCacheHelper;
use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\Menu\Page;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * InfoBox to inform about current query cache
 */
class QueryCacheStatusInfoBox extends AbstractInfoBox
{
    protected $pageIdentifier = 'queryCache';

    protected $title = 'Query Cache Status';

    public function renderBody(Page $page): string
    {
        $this->setState(StateEnumeration::STATE_INFO);

        if (!$this->getQueryCacheHelper()->isQueryCacheEnabled($page)) {
            return 'Query Cache is not activated';
        }

        if ((int)($page->getVariables()['query_cache_size']) === 0) {
            return 'Query Cache is activated, but query_cache_size can not be 0';
        }

        return 'Query Cache is activated';
    }

    protected function getQueryCacheHelper(): QueryCacheHelper
    {
        return GeneralUtility::makeInstance(QueryCacheHelper::class);
    }
}
