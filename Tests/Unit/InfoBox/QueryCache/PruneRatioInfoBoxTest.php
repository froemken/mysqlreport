<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Tests\Unit\InfoBox\QueryCache;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use PHPUnit\Framework\Attributes\Test;
use StefanFroemken\Mysqlreport\Domain\Model\StatusValues;
use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use StefanFroemken\Mysqlreport\Helper\QueryCacheHelper;
use StefanFroemken\Mysqlreport\InfoBox\QueryCache\PruneRatioInfoBox;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class PruneRatioInfoBoxTest extends UnitTestCase
{
    private QueryCacheHelper $queryCacheHelper;
    private PruneRatioInfoBox $infoBox;
    private AbstractPlatform $platform;

    protected function setUp(): void
    {
        parent::setUp();
        $this->queryCacheHelper = new QueryCacheHelper();
        $this->infoBox = new PruneRatioInfoBox($this->queryCacheHelper);
        $this->platform = $this->createMock(AbstractPlatform::class);
    }

    #[Test]
    public function isAvailableReturnsFalseWhenQueryCacheIsDisabled(): void
    {
        $variables = new Variables([
            'query_cache_type' => 'OFF',
            'query_cache_size' => '0',
        ]);
        $statusValues = new StatusValues([]);

        self::assertFalse($this->infoBox->isAvailable($this->platform, $variables, $statusValues));
    }

    #[Test]
    public function getBodyReturnsEmptyWhenInsertsIsZero(): void
    {
        $variables = new Variables([
            'query_cache_type' => 'ON',
            'query_cache_size' => '16777216',
        ]);
        $statusValues = new StatusValues([
            'Qcache_inserts' => 0,
            'Qcache_lowmem_prunes' => 0,
        ]);

        self::assertSame('', $this->infoBox->getBody($statusValues, $variables));
    }

    #[Test]
    public function getStateEvaluatesThresholdsCorrectly(): void
    {
        $variables = new Variables([
            'query_cache_type' => 'ON',
            'query_cache_size' => '16777216',
        ]);

        $statusLowPrunes = new StatusValues(['Qcache_inserts' => 100, 'Qcache_lowmem_prunes' => 10]);
        $statusMedPrunes = new StatusValues(['Qcache_inserts' => 100, 'Qcache_lowmem_prunes' => 25]);
        $statusHighPrunes = new StatusValues(['Qcache_inserts' => 100, 'Qcache_lowmem_prunes' => 50]);

        self::assertSame(ContextualFeedbackSeverity::OK, $this->infoBox->getState($statusLowPrunes, $variables));
        self::assertSame(ContextualFeedbackSeverity::WARNING, $this->infoBox->getState($statusMedPrunes, $variables));
        self::assertSame(ContextualFeedbackSeverity::ERROR, $this->infoBox->getState($statusHighPrunes, $variables));
    }
}
