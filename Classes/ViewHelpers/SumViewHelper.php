<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\ViewHelpers;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * VH to sum some values
 */
class SumViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        $this->registerArgument(
            'profiles',
            'array',
            'Profile records',
            true
        );
        $this->registerArgument(
            'field',
            'string',
            'Field',
            false,
            'summed_duration'
        );
    }

    public function render(): string
    {
        $sum = 0;
        foreach ($this->arguments['profiles'] as $profile) {
            $sum += $profile[$this->arguments['field']];
        }

        return (string)$sum;
    }
}
