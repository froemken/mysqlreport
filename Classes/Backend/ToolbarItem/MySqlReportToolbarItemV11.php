<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Backend\ToolbarItem;

use Psr\Http\Message\ServerRequestInterface;
use StefanFroemken\Mysqlreport\Configuration\ExtConf;
use TYPO3\CMS\Backend\Toolbar\ToolbarItemInterface;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Toolbar item to inform admins about activated "Add EXPLAIN" option in
 * extension settings of EXT:mysqlreport
 */
class MySqlReportToolbarItemV11 implements ToolbarItemInterface
{
    private ExtConf $extConf;

    public function __construct(ExtConf $extConf)
    {
        $this->extConf = $extConf;
    }

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    /**
     * Checks whether the user has access to this toolbar item
     */
    public function checkAccess(): bool
    {
        return $this->getBackendUser()->isAdmin();
    }

    /**
     * Render "item" part of this toolbar
     */
    public function getItem(): string
    {
        return $this
            ->getFluidTemplateObject('MySqlReportToolbarItem.html')
            ->assign('addExplain', $this->extConf->isAddExplain())
            ->render();
    }

    /**
     * TRUE if this toolbar item has a collapsible drop-down
     */
    public function hasDropDown(): bool
    {
        return true;
    }

    /**
     * Render "drop down" part of this toolbar
     */
    public function getDropDown(): string
    {
        if ($this->extConf->isAddExplain()) {
            return '<h3 class="dropdown-headline">MySQL Report</h3>'
                . '<p class="dropdown-text">'
                . 'In extension settings of EXT:mysqlreport the option "Add EXPLAIN" is active!'
                . ' As that option will reset mysqli information like insert_id and affected_rows of previous queries'
                . ' it may break your TYPO3 system. F.E you can not create new scheduler tasks.'
                . ' Please deactivate that option as soon as possible.'
                . '</p>';
        }

        return '<h3 class="dropdown-headline">MySQL Report</h3>'
            . '<p class="dropdown-text">'
            . 'No problems with EXT:mysqlreport detected.'
            . '</p>';
    }

    /**
     * Returns an array with additional attributes added to containing <li> tag of the item.
     */
    public function getAdditionalAttributes(): array
    {
        return [];
    }

    /**
     * Returns an integer between 0 and 100 to determine
     * the position of this item relative to others
     */
    public function getIndex(): int
    {
        return 70;
    }

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * Returns a new standalone view, shorthand function
     */
    protected function getFluidTemplateObject(string $filename): StandaloneView
    {
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplateRootPaths(['EXT:mysqlreport/Resources/Private/Templates/ToolbarItem']);
        $view->setTemplate($filename);

        $view->getRequest()->setControllerExtensionName('Mysqlreport');
        return $view;
    }
}
