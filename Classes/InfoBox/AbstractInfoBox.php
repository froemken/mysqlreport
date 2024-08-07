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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Model with properties for panels you can see in BE module
 */
abstract class AbstractInfoBox implements \SplObserver
{
    protected string $pageIdentifier = '';

    /**
     * This is the title of the infobox
     */
    protected string $title = '';

    /**
     * Use addUnorderedListEntry to add new elements to <ul> output
     *
     * @var \SplQueue<array<string, string>>
     */
    private \SplQueue $unorderedList;

    /**
     * You can highlight the infobox with help of the state constants
     */
    private StateEnumeration $state;

    /**
     * You can decide, if this panel should be rendered or not.
     * Useful, if a MySQL status/variable does not exist.
     */
    protected bool $shouldBeRendered = true;

    protected string $template = 'EXT:mysqlreport/Resources/Private/Templates/InfoBox/Default.html';

    protected StandaloneView $view;

    public function __construct()
    {
        $this->unorderedList = new \SplQueue();
        $this->state = StateEnumeration::STATE_NOTICE;

        // Do not load StandaloneView with injectStandaloneView as it is not configured as "shared: false" anymore
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
        $this->view->setTemplatePathAndFilename($this->template);
        $this->view->assign('title', $this->title);
    }

    public function update(\SplSubject $subject): void
    {
        if ($subject instanceof Page) {
            $this->view->assign('body', $this->renderBody($subject));
            $this->view->assign('unorderedList', $this->unorderedList);

            // First call __string() of Enumeration, then cast to INT
            $this->view->assign('state', $this->state->value);

            // $shouldBeRendered can be modified within renderBody() by a developer
            if ($this->shouldBeRendered) {
                $subject->addInfoBoxView($this->view);
            }
        }
    }

    public function getPageIdentifier(): string
    {
        return $this->pageIdentifier;
    }

    protected function addUnorderedListEntry(string $value, string $title = ''): void
    {
        $this->unorderedList->enqueue([
            'title' => $title,
            'value' => $value,
        ]);
    }

    protected function setState(StateEnumeration $state): void
    {
        if ($this->state->value !== $state->value) {
            $this->state = $state;
        }
    }

    abstract protected function renderBody(Page $page): string;
}
