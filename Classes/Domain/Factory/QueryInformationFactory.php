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
use StefanFroemken\Mysqlreport\Traits\Typo3RequestTrait;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * A factory to build new Profile objects.
 * It should prevent calling methods to retrieve environment variables multiple times
 */
class QueryInformationFactory
{
    use Typo3RequestTrait;

    private int $pageUid;

    private string $ip;

    private string $referer;

    private string $request;

    private string $mode;

    private string $uniqueCallIdentifier;

    private int $crdate;

    public function __construct()
    {
        $this->pageUid = $this->getPageUid();
        $this->ip = GeneralUtility::getIndpEnv('REMOTE_ADDR');
        $this->referer = GeneralUtility::getIndpEnv('HTTP_REFERER');
        $this->request = GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL');
        $this->mode = $this->getTypo3Mode();
        $this->uniqueCallIdentifier = uniqid('', true);
        $this->crdate = (int)$GLOBALS['EXEC_TIME'];
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

    private function getPageUid(): int
    {
        $serverRequest = $GLOBALS['TYPO3_REQUEST'];
        if ($serverRequest instanceof ServerRequestInterface) {
            $pageArguments = $serverRequest->getAttribute('routing');
            if ($pageArguments instanceof PageArguments) {
                return $pageArguments->getPageId();
            }

            $backendPageUid = (int)($serverRequest->getQueryParams()['id'] ?? 0);
            if ($backendPageUid !== 0) {
                return $backendPageUid;
            }
        }

        return 0;
    }

    private function getTypo3Mode(): string
    {
        if (Environment::isCli()) {
            return 'CLI';
        }

        return $this->isBackendRequest() ? 'BE' : 'FE';
    }
}
