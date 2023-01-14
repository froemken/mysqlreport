<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use StefanFroemken\Mysqlreport\Helper\ModuleTemplateHelper;
use StefanFroemken\Mysqlreport\Menu\Page;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Controller to show a basic analysis of MySQL variables and status
 */
class MySqlReportController
{
    /**
     * The ServiceLocator loaded here is a container just containing a few objects
     * available for this controller
     */
    private ContainerInterface $serviceLocator;

    private ModuleTemplateFactory $moduleTemplateFactory;

    private ModuleTemplateHelper $moduleTemplateHelper;

    public function __construct(
        ContainerInterface $serviceLocator,
        ModuleTemplateFactory $moduleTemplateFactory,
        ModuleTemplateHelper $moduleTemplateHelper
    ) {
        $this->serviceLocator = $serviceLocator;
        $this->moduleTemplateFactory = $moduleTemplateFactory;
        $this->moduleTemplateHelper = $moduleTemplateHelper;
    }

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $this->moduleTemplateHelper->addOverviewButton($moduleTemplate->getDocHeaderComponent()->getButtonBar());

        $queryParameters = $request->getQueryParams();
        $actionMethod = ($queryParameters['action'] ?? '') . 'Action';

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplateRootPaths(['EXT:mysqlreport/Resources/Private/Templates']);
        $view->setPartialRootPaths(['EXT:mysqlreport/Resources/Private/Partials']);
        $view->setLayoutRootPaths(['EXT:mysqlreport/Resources/Private/Layouts']);

        if (method_exists($this, $actionMethod)) {
            $moduleTemplate->setContent(call_user_func([$this, $actionMethod], $moduleTemplate, $view));
        } else {
            $moduleTemplate->setContent($this->overviewAction($moduleTemplate, $view));
        }

        return new HtmlResponse($moduleTemplate->renderContent());
    }

    private function overviewAction(ModuleTemplate $moduleTemplate, StandaloneView $view): string
    {
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'system_mysqlreport',
            'MySqlReport Overview'
        );

        $view->setTemplate('MySqlReport/Overview');
        $view->assign(
            'status',
            $this->serviceLocator->get('repository.status')->findAll()
        );

        return $view->render();
    }

    private function informationAction(ModuleTemplate $moduleTemplate, StandaloneView $view): string
    {
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'system_mysqlreport',
            'MySqlReport Information',
            ['action' => 'information']
        );

        $view->setTemplate('MySqlReport/Information');
        $view->assign(
            'renderedInfoBoxes',
            $this->getPageByType('page.information')->getRenderedInfoBoxes()
        );

        return $view->render();
    }

    private function innoDbAction(ModuleTemplate $moduleTemplate, StandaloneView $view): string
    {
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'system_mysqlreport',
            'MySqlReport InnoDB',
            ['action' => 'innoDb']
        );

        $view->setTemplate('MySqlReport/Information');
        $view->assign(
            'renderedInfoBoxes',
            $this->getPageByType('page.innodb')->getRenderedInfoBoxes()
        );

        return $view->render();
    }

    private function threadCacheAction(ModuleTemplate $moduleTemplate, StandaloneView $view): string
    {
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'system_mysqlreport',
            'MySqlReport Thread Cache',
            ['action' => 'threadCache']
        );

        $view->setTemplate('MySqlReport/Information');
        $view->assign(
            'renderedInfoBoxes',
            $this->getPageByType('page.thread_cache')->getRenderedInfoBoxes()
        );

        return $view->render();
    }

    private function tableCacheAction(ModuleTemplate $moduleTemplate, StandaloneView $view): string
    {
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'system_mysqlreport',
            'MySqlReport Table Cache',
            ['action' => 'tableCache']
        );

        $view->setTemplate('MySqlReport/Information');
        $view->assign(
            'renderedInfoBoxes',
            $this->getPageByType('page.table_cache')->getRenderedInfoBoxes()
        );

        return $view->render();
    }

    private function queryCacheAction(ModuleTemplate $moduleTemplate, StandaloneView $view): string
    {
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'system_mysqlreport',
            'MySqlReport Query Cache',
            ['action' => 'queryCache']
        );

        $view->setTemplate('MySqlReport/Information');
        $view->assign(
            'renderedInfoBoxes',
            $this->getPageByType('page.query_cache')->getRenderedInfoBoxes()
        );

        return $view->render();
    }

    private function miscAction(ModuleTemplate $moduleTemplate, StandaloneView $view): string
    {
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'system_mysqlreport',
            'MySqlReport Misc',
            ['action' => 'misc']
        );

        $view->setTemplate('MySqlReport/Information');
        $view->assign(
            'renderedInfoBoxes',
            $this->getPageByType('page.misc')->getRenderedInfoBoxes()
        );

        return $view->render();
    }

    private function getPageByType(string $type): Page
    {
        return $this->serviceLocator->get($type);
    }
}
