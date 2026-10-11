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
use StefanFroemken\Mysqlreport\InfoBox\QueryCache\QueryCacheStatusInfoBox;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class QueryCacheStatusInfoBoxTest extends UnitTestCase
{
    private QueryCacheHelper $queryCacheHelper;
    private QueryCacheStatusInfoBox $infoBox;
    private AbstractPlatform $platform;

    protected function setUp(): void
    {
        parent::setUp();
        $this->queryCacheHelper = new QueryCacheHelper();
        $this->infoBox = new QueryCacheStatusInfoBox($this->queryCacheHelper);
        $this->platform = $this->createMock(AbstractPlatform::class);
    }

    #[Test]
    public function isAvailableReturnsFalseWhenQueryCacheUnsupported(): void
    {
        $variables = new Variables(['version' => '8.0.32']);
        $statusValues = new StatusValues([]);

        self::assertFalse($this->infoBox->isAvailable($this->platform, $variables, $statusValues));
    }

    #[Test]
    public function isAvailableReturnsTrueWhenQueryCacheSupported(): void
    {
        $variables = new Variables(['query_cache_type' => 'OFF']);
        $statusValues = new StatusValues([]);

        self::assertTrue($this->infoBox->isAvailable($this->platform, $variables, $statusValues));
    }

    #[Test]
    public function getBodyAndStateWhenDisabledProvidesPositiveFeedback(): void
    {
        $variables = new Variables([
            'query_cache_type' => 'OFF',
            'query_cache_size' => '0',
        ]);
        $statusValues = new StatusValues([]);

        $body = $this->infoBox->getBody($statusValues, $variables);
        $state = $this->infoBox->getState($statusValues, $variables);

        self::assertStringContainsString('The Query Cache is disabled', $body);
        self::assertStringContainsString('recommended best practice', $body);
        self::assertStringContainsString('mutex', $body);
        self::assertSame(ContextualFeedbackSeverity::OK, $state);
    }

    #[Test]
    public function getBodyAndStateWhenEnabledInformsAboutMutexContention(): void
    {
        $variables = new Variables([
            'query_cache_type' => 'ON',
            'query_cache_size' => '16777216',
        ]);
        $statusValues = new StatusValues([]);

        $body = $this->infoBox->getBody($statusValues, $variables);
        $state = $this->infoBox->getState($statusValues, $variables);

        self::assertStringContainsString('The Query Cache is enabled', $body);
        self::assertStringContainsString('mutex', $body);
        self::assertSame(ContextualFeedbackSeverity::INFO, $state);
    }
}
