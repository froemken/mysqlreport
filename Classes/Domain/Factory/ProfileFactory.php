<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Domain\Factory;

use StefanFroemken\Mysqlreport\Domain\Model\Profile;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * A factory to build new Profile objects.
 * It should prevent calling methods to retrieve environment variables multiple times
 */
class ProfileFactory
{
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

    public function createNewProfile(): Profile
    {
        $profile = new Profile();
        $profile->setPid($this->pageUid);
        $profile->setIp($this->ip);
        $profile->setReferer($this->referer);
        $profile->setRequest($this->request);
        $profile->setMode($this->mode);
        $profile->setUniqueCallIdentifier($this->uniqueCallIdentifier);
        $profile->setCrdate($this->crdate);

        return $profile;
    }

    private function getPageUid(): int
    {
        return isset($GLOBALS['TSFE']) && $GLOBALS['TSFE'] instanceof TypoScriptFrontendController
            ? $GLOBALS['TSFE']->id
            : 0;
    }

    private function getTypo3Mode(): string
    {
        return GeneralUtility::getIndpEnv('SCRIPT_NAME') === '/typo3/index.php' ? 'BE' : 'FE';
    }
}
