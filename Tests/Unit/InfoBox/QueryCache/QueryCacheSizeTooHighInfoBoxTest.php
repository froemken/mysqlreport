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
use StefanFroemken\Mysqlreport\InfoBox\QueryCache\QueryCacheSizeTooHighInfoBox;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class QueryCacheSizeTooHighInfoBoxTest extends UnitTestCase
{
    private QueryCacheHelper $queryCacheHelper;
    private QueryCacheSizeTooHighInfoBox $infoBox;
    private AbstractPlatform&MockObject $platform;

    protected function setUp(): void
    {
        parent::setUp();
        $this->queryCacheHelper = new QueryCacheHelper();
        $this->infoBox = new QueryCacheSizeTooHighInfoBox($this->queryCacheHelper);
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
    public function getBodyReturnsEmptyWhenSizeUnder128MB(): void
    {
        $variables = new Variables([
            'query_cache_type' => 'ON',
            'query_cache_size' => '67108864', // 64 MB
        ]);
        $statusValues = new StatusValues([]);

        self::assertSame('', $this->infoBox->getBody($statusValues, $variables));
    }

    #[Test]
    public function getBodyReturnsWarningWhenSizeBetween128MBAnd256MB(): void
    {
        $variables = new Variables([
            'query_cache_type' => 'ON',
            'query_cache_size' => '201326592', // 192 MB
        ]);
        $statusValues = new StatusValues([]);

        $body = $this->infoBox->getBody($statusValues, $variables);
        $state = $this->infoBox->getState($statusValues, $variables);

        self::assertStringContainsString('higher than recommended', $body);
        self::assertSame(ContextualFeedbackSeverity::WARNING, $state);
    }

    #[Test]
    public function getStateReturnsErrorWhenSizeExceeds256MB(): void
    {
        $variables = new Variables([
            'query_cache_type' => 'ON',
            'query_cache_size' => '536870912', // 512 MB
        ]);
        $statusValues = new StatusValues([]);

        $state = $this->infoBox->getState($statusValues, $variables);
        self::assertSame(ContextualFeedbackSeverity::ERROR, $state);
    }
}
