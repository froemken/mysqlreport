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
use TYPO3\CMS\Core\View\ViewInterface;

/**
 * Model with properties for panels you can see in BE module
 */
abstract class AbstractInfoBox
{
    protected const TITLE = '';

    public function __construct(
        protected readonly StatusValues $statusValues,
        protected readonly Variables $variables,
    ) {}

    abstract public function renderBody(): string;

    protected function getStatusValues(): StatusValues
    {
        return $this->statusValues;
    }

    protected function getVariables(): Variables
    {
        return $this->variables;
    }

    public function updateView(ViewInterface $view): ?ViewInterface
    {
        if (($body = $this->renderBody()) === '') {
            return null;
        }

        $view->assign('title', static::TITLE);
        $view->assign('body', $body);

        if ($this instanceof InfoBoxUnorderedListInterface) {
            $view->assign('unorderedList', $this->getUnorderedList());
        }

        if ($this instanceof InfoBoxStateInterface) {
            $view->assign('state', $this->getState()->value);
        }

        return $view;
    }
}
