<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Enumeration;

/**
 * Contains the state values from InfoBox ViewHelper for highlighting
 */
enum StateEnumeration: int
{
    case STATE_NOTICE = -2;
    case STATE_INFO = -1;
    case STATE_OK = 0;
    case STATE_WARNING = 1;
    case STATE_ERROR = 2;
}
