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
use StefanFroemken\Mysqlreport\Helper\QueryCacheHelper;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxInterface;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxStateInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;

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
        private QueryCacheHelper $queryCacheHelper,
    ) {}

    public function getBody(StatusValues $statusValues, Variables $variables): string
    {
        if (!$this->queryCacheHelper->isQueryCacheEnabled($variables)) {
            return 'Query Cache is not activated';
        }

        if ((int)($variables['query_cache_size']) === 0) {
            return 'Query Cache is activated, but query_cache_size can not be 0';
        }

        return 'Query Cache is activated';
    }

    public function getState(StatusValues $statusValues, Variables $variables): ContextualFeedbackSeverity
    {
        return ContextualFeedbackSeverity::INFO;
    }
}
