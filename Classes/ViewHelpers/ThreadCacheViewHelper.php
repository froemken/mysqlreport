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

    /**
     * analyze QueryCache parameters
     *
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Status $status
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Variables $variables
     * @return string
     */
    public function render(\StefanFroemken\Mysqlreport\Domain\Model\Status $status, \StefanFroemken\Mysqlreport\Domain\Model\Variables $variables)
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
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Status $status
     * @return array
     */
    protected function getHitRatio(\StefanFroemken\Mysqlreport\Domain\Model\Status $status)
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
