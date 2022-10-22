<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox;

use StefanFroemken\Mysqlreport\Enumeration\StateEnumeration;
use StefanFroemken\Mysqlreport\Menu\Page;
use TYPO3\CMS\Core\Type\Exception\InvalidEnumerationValueException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Model with properties for panels you can see in BE module
 */
abstract class AbstractInfoBox implements \SplObserver
{
    /**
     * @var string
     */
    protected $pageIdentifier = '';

    /**
     * This is the title of the infobox
     *
     * @var string
     */
    protected $title = '';

    /**
     * Use addUnorderedListEntry to add new elements to <ul> output
     *
     * @var \SplQueue
     */
    private $unorderedList;

    /**
     * You can highlight the infobox with help of the state constants
     *
     * @var StateEnumeration
     */
    private $state;

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
    protected $template = 'EXT:mysqlreport/Resources/Private/Templates/InfoBox/Default.html';

    /**
     * @var StandaloneView
     */
    protected $view;

    public function __construct()
    {
        $this->unorderedList = new \SplQueue();
        $this->state = new StateEnumeration();

        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
        $this->view->setTemplatePathAndFilename($this->template);
        $this->view->assign('title', $this->title);
    }

    public function update(\SplSubject $subject): void
    {
        if ($subject instanceof Page) {
            $this->view->assign('body', $this->renderBody($subject));
            $this->view->assign('unorderedList', $this->unorderedList);
            $this->view->assign('state', (string)$this->state);

            // $shouldBeRendered can be modified within renderBody() by a developer
            if ($this->shouldBeRendered) {
                $subject->addInfoBoxView($this->view);
            }
        }
    }

    protected function addUnorderedListEntry(string $value, string $title = ''): void
    {
        $this->unorderedList->enqueue([
            'title' => $title,
            'value' => $value
        ]);
    }

    protected function setState(int $state): void
    {
        if (!$this->state->equals($state)) {
            try {
                $this->state = new StateEnumeration($state);
            } catch (InvalidEnumerationValueException $invalidEnumerationValueException) {
                // Do nothing, keep current color
            }
        }
    }

    abstract protected function renderBody(Page $page): string;
}
