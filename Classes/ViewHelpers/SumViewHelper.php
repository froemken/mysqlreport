<?php
namespace StefanFroemken\Mysqlreport\ViewHelpers;

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
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * @package mysqlreport
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
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
