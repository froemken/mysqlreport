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
use PHPUnit\Framework\MockObject\MockObject;
use StefanFroemken\Mysqlreport\Domain\Model\StatusValues;
use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use StefanFroemken\Mysqlreport\Helper\QueryCacheHelper;
use StefanFroemken\Mysqlreport\InfoBox\QueryCache\HitRatioInfoBox;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class HitRatioInfoBoxTest extends UnitTestCase
{
    private QueryCacheHelper $queryCacheHelper;
    private HitRatioInfoBox $infoBox;
    private AbstractPlatform&MockObject $platform;

    protected function setUp(): void
    {
        parent::setUp();
        $this->queryCacheHelper = new QueryCacheHelper();
        $this->infoBox = new HitRatioInfoBox($this->queryCacheHelper);
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
    public function isAvailableReturnsTrueWhenQueryCacheIsEnabled(): void
    {
        $variables = new Variables([
            'query_cache_type' => 'ON',
            'query_cache_size' => '16777216',
        ]);
        $statusValues = new StatusValues([]);

        self::assertTrue($this->infoBox->isAvailable($this->platform, $variables, $statusValues));
    }

    #[Test]
    public function getBodyReturnsEmptyWhenTotalSelectsIsZero(): void
    {
        $variables = new Variables([
            'query_cache_type' => 'ON',
            'query_cache_size' => '16777216',
        ]);
        $statusValues = new StatusValues([
            'Qcache_hits' => 0,
            'Com_select' => 0,
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

        $statusHighHit = new StatusValues(['Qcache_hits' => 80, 'Com_select' => 20]);
        $statusMedHit = new StatusValues(['Qcache_hits' => 30, 'Com_select' => 70]);
        $statusLowHit = new StatusValues(['Qcache_hits' => 10, 'Com_select' => 90]);

        self::assertSame(ContextualFeedbackSeverity::OK, $this->infoBox->getState($statusHighHit, $variables));
        self::assertSame(ContextualFeedbackSeverity::WARNING, $this->infoBox->getState($statusMedHit, $variables));
        self::assertSame(ContextualFeedbackSeverity::ERROR, $this->infoBox->getState($statusLowHit, $variables));
    }
}
