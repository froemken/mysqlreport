<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\Misc;

use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\Menu\Page;
use TYPO3\CMS\Core\Utility\CommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * InfoBox about back_log configuration
 */
class BackLogInfoBox extends AbstractInfoBox
{
    protected string $pageIdentifier = 'misc';

    protected string $title = 'Back Log';

    public function renderBody(Page $page): string
    {
        if (!isset($page->getVariables()['back_log'])) {
            $this->shouldBeRendered = false;
            return '';
        }

        $content = [];
        $content[] = 'The back_log is a small buffer, which holds all network requests to the Server.';
        $content[] = 'It will increase if you have many requests at the same time.';
        $content[] = 'The back_log is limited to the max requests your operating system can handle.';
        $content[] = "\n\n";
        $content[] = 'Current Back_log: %s';
        $content[] = "\n\n";
        $content[] = 'Max allowed Request by operating system: %s';

        return sprintf(
            implode(' ', $content),
            $page->getVariables()['back_log'],
            $this->getMaxNetworkRequests()
        );
    }

    /**
     * Execute shell command to get amount of max network requests by OS
     */
    protected function getMaxNetworkRequests(): string
    {
        $value = '';
        $command = CommandUtility::getCommand('sysctl');
        $lastLine = CommandUtility::exec($command . ' net.core.somaxconn');

        if ($lastLine === '') {
            // maybe we are on a MAC
            $lastLine = CommandUtility::exec($command . ' kern.ipc.somaxconn');
        }

        if ($lastLine !== '' && strpos($lastLine, ':') !== false) {
            $parts = GeneralUtility::trimExplode(':', $lastLine);
            $value = $parts[1];
        } elseif ($lastLine !== '' && strpos($lastLine, '=') !== false) {
            $parts = GeneralUtility::trimExplode('=', $lastLine);
            $value = $parts[1];
        }

        return $value;
    }
}
