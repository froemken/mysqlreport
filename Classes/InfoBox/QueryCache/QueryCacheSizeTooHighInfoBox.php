<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\QueryCache;

use StefanFroemken\Mysqlreport\Domain\Model\StatusValues;
use StefanFroemken\Mysqlreport\Domain\Model\Variables;

use StefanFroemken\Mysqlreport\Enumeration\StateEnumeration;
use StefanFroemken\Mysqlreport\Helper\QueryCacheHelper;
use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxStateInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * InfoBox to inform about the current query cache too high
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.query_cache',
)]
final readonly class QueryCacheSizeTooHighInfoBox extends AbstractInfoBox implements InfoBoxStateInterface
{
    public function __construct(
        private StatusValues $statusValues,
        private Variables $variables,
    ) {}


    public const TITLE = 'Query Cache too high';

    private QueryCacheHelper $queryCacheHelper;

    public function injectQueryCacheHelper(QueryCacheHelper $queryCacheHelper): void
    {
        $this->queryCacheHelper = $queryCacheHelper;
    }

    public function getBody(): string
    {
        if (
            !isset($this->variables['query_cache_size'])
            || !$this->queryCacheHelper->isQueryCacheEnabled($this->variables)
        ) {
            return '';
        }

        if ($this->variables['query_cache_size'] < 268435456) {
            return '';
        }

        $content = [];
        $content[] = 'The query cache is configured too high.';
        $content[] = 'Because of concurrency it may take too long to delete queries from Query Cache.';
        $content[] = 'Try to keep query cache below 256MBAs higher as better.';

        return implode(' ', $content);
    }

    public function getState(): StateEnumeration
    {
        return StateEnumeration::STATE_ERROR;
    }
}
