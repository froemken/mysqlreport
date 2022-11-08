<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Enumeration;

use TYPO3\CMS\Core\Type\Enumeration;

/**
 * Contains the state values from InfoBox ViewHelper for highlighting
 */
final class StateEnumeration extends Enumeration
{
    public const __default = self::STATE_NOTICE;
    public const STATE_NOTICE = -2;
    public const STATE_INFO = -1;
    public const STATE_OK = 0;
    public const STATE_WARNING = 1;
    public const STATE_ERROR = 2;
}
