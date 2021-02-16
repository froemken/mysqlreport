<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Controller;

use StefanFroemken\Mysqlreport\Domain\Repository\StatusRepository;
use StefanFroemken\Mysqlreport\Domain\Repository\VariablesRepository;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Lang\LanguageService;

/**
 * Abstract Controller with useful methods like adding buttons to BE view
 */
abstract class AbstractController extends ActionController
{
    protected function initializeView(ViewInterface $view)
    {
        $uriBuilder = $this->objectManager->get(UriBuilder::class);
        $uriBuilder->setRequest($this->request);

        /** @var BackendTemplateView $view */
        parent::initializeView($view);

        $menu = $view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('mysqlreport');

        $actions = [
            [
                'controller' => 'MySql',
                'action' => 'index',
                'label' => 'Introduction'
            ],
            [
                'controller' => 'MySql',
                'action' => 'queryCache',
                'label' => 'Query Cache'
            ],
            [
                'controller' => 'MySql',
                'action' => 'innoDbBuffer',
                'label' => 'InnoDB Buffer'
            ],
            [
                'controller' => 'MySql',
                'action' => 'threadCache',
                'label' => 'Threads Cache'
            ],
            [
                'controller' => 'MySql',
                'action' => 'tableCache',
                'label' => 'Table Cache'
            ],
            [
                'controller' => 'Profile',
                'action' => 'list',
                'label' => 'Profiling'
            ],
            [
                'controller' => 'Query',
                'action' => 'filesort',
                'label' => 'Queries using Filesort'
            ],
            [
                'controller' => 'Query',
                'action' => 'fullTableScan',
                'label' => 'Queries with FTS'
            ],
        ];

        foreach ($actions as $action) {
            $item = $menu->makeMenuItem()
                ->setTitle($action['label'])
                ->setHref($uriBuilder->reset()->uriFor($action['action'], [], $action['controller']))
                ->setActive($this->request->getControllerActionName() === $action['action']);
            $menu->addMenuItem($item);
        }

        $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);

        // Shortcut
        $buttonBar = $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();
        if ($this->getBackendUser()->mayMakeShortcut()) {
            $shortcutButton = $buttonBar->makeShortcutButton()
                ->setModuleName('system_MysqlreportMysql')
                ->setGetVariables(['route', 'module', 'id'])
                ->setDisplayName('Shortcut');
            $buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);
        }
    }

    /**
     * @return BackendUserAuthentication
     */
    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
