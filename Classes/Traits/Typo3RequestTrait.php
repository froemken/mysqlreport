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
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\Routing\BackendEntryPointResolver;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Trait to get the officially TYPO3 request object or create a new empty TYPO3 request.
 */
trait Typo3RequestTrait
{
    /**
     * Returns the TYPO3 request.
     * As logger will be created at a very early state TYPO3_REQUEST may not be initialized yet.
     * That's why we fall back to create our own local request.
     */
    private function getTypo3Request(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'] ?? ServerRequestFactory::fromGlobals();
    }

    /**
     * In ProfileFactory we try to get the ApplicationType from request.
     * But as "applicationType" may not be set in TYPO3 request until now,
     * I try to retrieve BE/FE mode from BackendEntryPointResolver directly.
     * That should also work with a nearly empty TYPO3 request object (see method getTypo3Request)
     */
    private function isBackendRequest(): bool
    {
        return GeneralUtility::makeInstance(BackendEntryPointResolver::class)
            ->isBackendRoute($this->getTypo3Request());
    }
}
