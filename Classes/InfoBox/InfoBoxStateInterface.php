<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox;

use StefanFroemken\Mysqlreport\Domain\Model\StatusValues;
use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;

interface InfoBoxStateInterface
{
    public function getState(StatusValues $statusValues, Variables $variables): ContextualFeedbackSeverity;
}
