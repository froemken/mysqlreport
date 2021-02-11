<?php
namespace StefanFroemken\Mysqlreport\ViewHelpers\Command;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use TYPO3\CMS\Core\Utility\CommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

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
    public function render()
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
        return $value;
    }
}
