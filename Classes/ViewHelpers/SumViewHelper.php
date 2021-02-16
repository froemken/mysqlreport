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
    /**
     * analyze QueryCache parameters
     *
     * @param array $profiles
     * @param string $field
     * @return string
     */
    public function render(array $profiles, $field = 'summed_duration')
    {
        $sum = 0;
        foreach ($profiles as $profile) {
            $sum += $profile[$field];
        }
        return $sum;
    }
}
