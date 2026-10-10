<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Domain\Factory;

use Psr\Http\Message\ServerRequestInterface;
use StefanFroemken\Mysqlreport\Domain\Model\QueryInformation;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3\CMS\Core\Routing\PageArguments;

/**
 * A factory to build new Profile objects.
 * It should prevent calling methods to retrieve environment variables multiple times
 */
final readonly class QueryInformationFactory
{
    private int $pageUid;

    private string $ip;

    private string $referer;

    private string $request;

    private string $mode;

    private string $uniqueCallIdentifier;

    private int $crdate;

    public function __construct(
        private Context $context,
    ) {
        $serverRequest = $GLOBALS['TYPO3_REQUEST'] ?? null;
        $request = $serverRequest instanceof ServerRequestInterface ? $serverRequest : null;

        $this->pageUid = $this->resolvePageUid($request);
        $this->ip = $this->resolveIp($request);
        $this->referer = $this->resolveReferer($request);
        $this->request = $this->resolveRequestUrl($request);
        $this->mode = $this->resolveMode($request);
        $this->uniqueCallIdentifier = uniqid('', true);
        $this->crdate = (int)$this->context->getPropertyFromAspect('date', 'timestamp');
    }

    public function createNewQueryInformation(): QueryInformation
    {
        $queryInformation = new QueryInformation();
        $queryInformation->setPid($this->pageUid);
        $queryInformation->setIp($this->ip);
        $queryInformation->setReferer($this->referer);
        $queryInformation->setRequest($this->request);
        $queryInformation->setMode($this->mode);
        $queryInformation->setUniqueCallIdentifier($this->uniqueCallIdentifier);
        $queryInformation->setCrdate($this->crdate);

        return $queryInformation;
    }

    private function resolvePageUid(?ServerRequestInterface $request): int
    {
        if ($request instanceof ServerRequestInterface) {
            $pageArguments = $request->getAttribute('routing');
            if ($pageArguments instanceof PageArguments) {
                return $pageArguments->getPageId();
            }
        }

        $backendPageUid = (int)($_GET['id'] ?? 0);
        if ($backendPageUid !== 0) {
            return $backendPageUid;
        }

        return 0;
    }

    private function resolveIp(?ServerRequestInterface $request): string
    {
        if (!$request instanceof ServerRequestInterface) {
            return '';
        }
        $normalizedParams = $request->getAttribute('normalizedParams');
        if ($normalizedParams instanceof NormalizedParams) {
            return $normalizedParams->getRemoteAddress();
        }
        return '';
    }

    private function resolveReferer(?ServerRequestInterface $request): string
    {
        if (!$request instanceof ServerRequestInterface) {
            return '';
        }
        $normalizedParams = $request->getAttribute('normalizedParams');
        if ($normalizedParams instanceof NormalizedParams) {
            return $normalizedParams->getHttpReferer();
        }
        return '';
    }

    private function resolveRequestUrl(?ServerRequestInterface $request): string
    {
        if (!$request instanceof ServerRequestInterface) {
            return '';
        }
        $normalizedParams = $request->getAttribute('normalizedParams');
        if ($normalizedParams instanceof NormalizedParams) {
            return $normalizedParams->getRequestUrl();
        }
        return '';
    }

    private function resolveMode(?ServerRequestInterface $request): string
    {
        if (Environment::isCli()) {
            return 'CLI';
        }

        if ($request instanceof ServerRequestInterface) {
            $applicationType = ApplicationType::fromRequest($request);
            return $applicationType->isBackend() ? 'BE' : 'FE';
        }

        return 'FE';
    }
}
