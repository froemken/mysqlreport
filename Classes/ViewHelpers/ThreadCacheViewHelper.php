<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\ViewHelpers;

use StefanFroemken\Mysqlreport\Domain\Model\Status;
use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * VH which adds variables regarding ThreadCache to template
 */
class ThreadCacheViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeChildren = false;

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    public function render(Status $status, Variables $variables): string
    {
        $this->templateVariableContainer->add('hitRatio', $this->getHitRatio($status));
        $content = $this->renderChildren();
        $this->templateVariableContainer->remove('hitRatio');
        return $content;
    }

    /**
     * get hit ratio of threads cache
     * A ratio nearly 100 would be cool
     *
     * @param Status $status
     * @return array
     */
    protected function getHitRatio(Status $status): array
    {
        $result = [];
        $hitRatio = 100 - (($status->getThreadsCreated() / $status->getConnections()) * 100);
        if ($hitRatio <= 80) {
            $result['status'] = 'danger';
        } elseif ($hitRatio <= 95) {
            $result['status'] = 'warning';
        } else {
            $result['status'] = 'success';
        }
        $result['value'] = round($hitRatio, 2);
        return $result;
    }
}
