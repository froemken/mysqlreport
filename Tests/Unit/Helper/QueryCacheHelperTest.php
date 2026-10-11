<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Tests\Unit\Helper;

use PHPUnit\Framework\Attributes\Test;
use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use StefanFroemken\Mysqlreport\Helper\QueryCacheHelper;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class QueryCacheHelperTest extends UnitTestCase
{
    private QueryCacheHelper $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new QueryCacheHelper();
    }

    #[Test]
    public function isQueryCacheSupportedReturnsFalseWhenHaveQueryCacheIsNo(): void
    {
        $variables = new Variables([
            'have_query_cache' => 'NO',
            'query_cache_type' => 'OFF',
        ]);

        self::assertFalse($this->helper->isQueryCacheSupported($variables));
    }

    #[Test]
    public function isQueryCacheSupportedReturnsFalseWhenVariablesMissing(): void
    {
        $variables = new Variables([
            'version' => '8.0.32',
        ]);

        self::assertFalse($this->helper->isQueryCacheSupported($variables));
    }

    #[Test]
    public function isQueryCacheSupportedReturnsTrueWhenQueryCacheTypeExists(): void
    {
        $variables = new Variables([
            'have_query_cache' => 'YES',
            'query_cache_type' => 'OFF',
        ]);

        self::assertTrue($this->helper->isQueryCacheSupported($variables));
    }

    #[Test]
    public function isQueryCacheEnabledReturnsFalseWhenUnsupported(): void
    {
        $variables = new Variables(['version' => '8.0.32']);

        self::assertFalse($this->helper->isQueryCacheEnabled($variables));
    }

    #[Test]
    public function isQueryCacheEnabledReturnsFalseWhenSizeIsZero(): void
    {
        $variables = new Variables([
            'have_query_cache' => 'YES',
            'query_cache_type' => 'ON',
            'query_cache_size' => '0',
        ]);

        self::assertFalse($this->helper->isQueryCacheEnabled($variables));
    }

    #[Test]
    public function isQueryCacheEnabledReturnsFalseWhenTypeIsOff(): void
    {
        $variables = new Variables([
            'have_query_cache' => 'YES',
            'query_cache_type' => 'OFF',
            'query_cache_size' => '16777216',
        ]);

        self::assertFalse($this->helper->isQueryCacheEnabled($variables));
    }

    #[Test]
    public function isQueryCacheEnabledReturnsTrueWhenTypeIsOnAndSizeGreaterThanZero(): void
    {
        $variables = new Variables([
            'have_query_cache' => 'YES',
            'query_cache_type' => 'ON',
            'query_cache_size' => '16777216',
        ]);

        self::assertTrue($this->helper->isQueryCacheEnabled($variables));
    }

    #[Test]
    public function isQueryCacheEnabledReturnsTrueForNumericOneAndDemand(): void
    {
        $variablesDemand = new Variables([
            'have_query_cache' => 'YES',
            'query_cache_type' => 'DEMAND',
            'query_cache_size' => '16777216',
        ]);
        $variablesNumeric = new Variables([
            'have_query_cache' => 'YES',
            'query_cache_type' => '1',
            'query_cache_size' => '16777216',
        ]);

        self::assertTrue($this->helper->isQueryCacheEnabled($variablesDemand));
        self::assertTrue($this->helper->isQueryCacheEnabled($variablesNumeric));
    }
}
