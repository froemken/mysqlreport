<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Panel;

use StefanFroemken\Mysqlreport\Domain\Model\Page;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Model with properties for panels you can see in BE module
 */
abstract class AbstractPanel implements \SplObserver
{
    /**
     * @var string
     */
    protected $pageIdentifier = '';

    /**
     * @var string
     */
    protected $header = '';

    /**
     * You can decide, if this panel should be rendered or not.
     * Useful, if a MySQL status/variable does not exist.
     *
     * @var bool
     */
    protected $shouldBeRendered = true;

    /**
     * @var string
     */
    protected $template = 'EXT:mysqlreport/Resources/Private/Templates/Panel/Default.html';

    /**
     * @var StandaloneView
     */
    protected $view;

    public function __construct()
    {
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
        $this->view->setTemplatePathAndFilename($this->template);
        $this->view->assign('header', $this->header);
    }

    public function update(\SplSubject $subject): void
    {
        if (
            $subject instanceof Page
            && $subject->getPageIdentifier() === $this->pageIdentifier
        ) {
            $body = $this->renderBody($subject);
            if ($this->shouldBeRendered) {
                $this->view->assign('body', $body);
                $subject->addRenderedPanel($this->view->render());
            }
        }
    }

    abstract protected function renderBody(Page $page): string;
}
