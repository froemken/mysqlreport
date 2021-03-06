<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

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

    public function initializeArguments()
    {
        $this->registerArgument(
            'status',
            'array',
            'Status of MySQL server',
            true
        );
        $this->registerArgument(
            'variables',
            'array',
            'Variables of MySQL server',
            true
        );
    }

    public function render(): string
    {
        $this->templateVariableContainer->add('hitRatio', $this->getHitRatio($this->arguments['status']));
        $content = $this->renderChildren();
        $this->templateVariableContainer->remove('hitRatio');

        return $content;
    }

    /**
     * get hit ratio of threads cache
     * A ratio nearly 100 would be cool
     *
     * @param array $status
     * @return array
     */
    protected function getHitRatio(array $status): array
    {
        $result = [];
        $hitRatio = 100 - (($status['Threads_created'] / $status['Connections']) * 100);
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
