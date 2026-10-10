<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Tests\Unit\Middleware;

use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use StefanFroemken\Mysqlreport\Middleware\QueryFlusherMiddleware;
use StefanFroemken\Mysqlreport\Service\QueryCollector;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class QueryFlusherMiddlewareTest extends UnitTestCase
{
    #[Test]
    public function processCallsHandlerAndFlushesQueryCollector(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);

        $queryCollector = $this->createMock(QueryCollector::class);
        $queryCollector->expects($this->once())->method('flush');

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willReturn($response);

        $middleware = new QueryFlusherMiddleware($queryCollector);
        $actualResponse = $middleware->process($request, $handler);

        self::assertSame($response, $actualResponse);
    }
}
