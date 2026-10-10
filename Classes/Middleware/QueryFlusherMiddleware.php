<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use StefanFroemken\Mysqlreport\Service\QueryCollector;

/**
 * PSR-15 Middleware that triggers flushing collected SQL queries after the response is generated.
 */
final readonly class QueryFlusherMiddleware implements MiddlewareInterface
{
    public function __construct(
        private QueryCollector $queryCollector,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $this->queryCollector->flush();

        return $response;
    }
}
