<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * VH which adds variables regarding InnoDB to template
 */
class InnoDbBufferViewHelper extends AbstractViewHelper
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
        $this->templateVariableContainer->add('writeRatio', $this->getWriteRatio($this->arguments['status']));
        $content = $this->renderChildren();
        $this->templateVariableContainer->remove('writeRatio');

        return $content;
    }


}
