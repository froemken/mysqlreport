<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\ViewHelpers\Command;

use TYPO3\CMS\Core\Utility\CommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * VH to execute a command to get amount of max network requests by OS
 */
class BackLogViewHelper extends AbstractViewHelper
{
    /**
     * execute a shell command to get the
     *
     * @link: http://dev.mysql.com/doc/refman/5.6/en/server-system-variables.html#sysvar_back_log
     * @link: http://forums.gentoo.org/viewtopic-p-7374046.html#7374046
     * @return string
     */
    public function render(): string
    {
        $value = '';
        $command = CommandUtility::getCommand('sysctl');
        $lastLine = CommandUtility::exec($command . ' net.core.somaxconn');
        if (empty($lastLine)) {
            // maybe we are on a MAC
            $lastLine = CommandUtility::exec($command . ' kern.ipc.somaxconn');
        }
        if (!empty($lastLine)) {
            $parts = GeneralUtility::trimExplode(':', $lastLine);
            $value = $parts[1];
        }
        return (string)$value;
    }
}
