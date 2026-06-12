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
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxInterface;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxStateInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * InfoBox to inform about the current query cache
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.query_cache',
    attributes: ['priority' => 90],
)]
final readonly class QueryCacheStatusInfoBox implements InfoBoxInterface, InfoBoxStateInterface
{
    public const TITLE = 'Query Cache Status';

    public function __construct(
        private StatusValues $statusValues,
        private Variables $variables,
    ) {}

    private QueryCacheHelper $queryCacheHelper;

    public function injectQueryCacheHelper(QueryCacheHelper $queryCacheHelper): void
    {
        $this->queryCacheHelper = $queryCacheHelper;
    }

    public function getBody(): string
    {
        if (!$this->queryCacheHelper->isQueryCacheEnabled($this->variables)) {
            return 'Query Cache is not activated';
        }

        if ((int)($this->variables['query_cache_size']) === 0) {
            return 'Query Cache is activated, but query_cache_size can not be 0';
        }

        return 'Query Cache is activated';
    }

    public function getState(): StateEnumeration
    {
        return StateEnumeration::STATE_INFO;
    }
}
