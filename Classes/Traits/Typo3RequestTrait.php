<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Traits;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\Routing\BackendEntryPointResolver;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Trait to get the official TYPO3 request object or create a new empty TYPO3 request.
 */
trait Typo3RequestTrait
{
    /**
     * Returns the TYPO3 request.
     * As the logger will be created at a very early state, TYPO3_REQUEST may not be initialized yet.
     * That's why we fall back creating our own local request.
     */
    private function getTypo3Request(): ServerRequestInterface
    {
        if (isset($GLOBALS['TYPO3_REQUEST'])) {
            return $GLOBALS['TYPO3_REQUEST'];
        }

        if (!Environment::isCli()) {
            return ServerRequestFactory::fromGlobals();
        }

        return new ServerRequest('https://www.typo3lexikon.de', 'GET');
    }

    /**
     * In ProfileFactory we try to get the ApplicationType from the request.
     * But as "applicationType" may not be set in the TYPO3 request until now,
     * I try to retrieve BE/FE mode from BackendEntryPointResolver directly.
     * That should also work with a nearly empty TYPO3 request object (see method getTypo3Request)
     */
    private function isBackendRequest(): bool
    {
        return GeneralUtility::makeInstance(BackendEntryPointResolver::class)
            ->isBackendRoute($this->getTypo3Request());
    }
}
