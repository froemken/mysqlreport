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
use StefanFroemken\Mysqlreport\Traits\GetStatusValuesAndVariablesTrait;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;

/**
 * Controller to show a basic analysis of MySQL variables and status
 */
class MySqlReportController
{
    use GetStatusValuesAndVariablesTrait;

    /**
     * @param ContainerInterface $serviceLocator A container just containing a few objects available for this controller
     */
    public function __construct(
        private ContainerInterface $serviceLocator,
        private ModuleTemplateFactory $moduleTemplateFactory,
        private ModuleTemplateHelper $moduleTemplateHelper,
    ) {}

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $this->moduleTemplateHelper->addOverviewButton($moduleTemplate->getDocHeaderComponent()->getButtonBar());

        $queryParameters = $request->getQueryParams();
        $actionMethod = ($queryParameters['action'] ?? '') . 'Action';

        if (method_exists($this, $actionMethod)) {
            return call_user_func([$this, $actionMethod], $moduleTemplate);
        }

        return $this->overviewAction($moduleTemplate);
    }

    private function overviewAction(ModuleTemplate $moduleTemplate): ResponseInterface
    {
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'system_mysqlreport',
            'MySqlReport Overview',
        );

        $moduleTemplate->assign('status', $this->getStatusValues());

        return $moduleTemplate->renderResponse('MySqlReport/Overview');
    }

    private function informationAction(ModuleTemplate $moduleTemplate): ResponseInterface
    {
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'system_mysqlreport',
            'MySqlReport Information',
            ['action' => 'information'],
        );

        $moduleTemplate->assign(
            'renderedInfoBoxes',
            $this->getPageByType('page.information')->getRenderedInfoBoxes(),
        );

        return $moduleTemplate->renderResponse('MySqlReport/Information');
    }

    private function innoDbAction(ModuleTemplate $moduleTemplate): ResponseInterface
    {
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'system_mysqlreport',
            'MySqlReport InnoDB',
            ['action' => 'innoDb'],
        );

        $moduleTemplate->assign(
            'renderedInfoBoxes',
            $this->getPageByType('page.innodb')->getRenderedInfoBoxes(),
        );

        return $moduleTemplate->renderResponse('MySqlReport/Information');
    }

    private function threadCacheAction(ModuleTemplate $moduleTemplate): ResponseInterface
    {
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'system_mysqlreport',
            'MySqlReport Thread Cache',
            ['action' => 'threadCache'],
        );

        $moduleTemplate->assign(
            'renderedInfoBoxes',
            $this->getPageByType('page.thread_cache')->getRenderedInfoBoxes(),
        );

        return $moduleTemplate->renderResponse('MySqlReport/Information');
    }

    private function tableCacheAction(ModuleTemplate $moduleTemplate): ResponseInterface
    {
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'system_mysqlreport',
            'MySqlReport Table Cache',
            ['action' => 'tableCache'],
        );

        $moduleTemplate->assign(
            'renderedInfoBoxes',
            $this->getPageByType('page.table_cache')->getRenderedInfoBoxes(),
        );

        return $moduleTemplate->renderResponse('MySqlReport/Information');
    }

    private function queryCacheAction(ModuleTemplate $moduleTemplate): ResponseInterface
    {
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'system_mysqlreport',
            'MySqlReport Query Cache',
            ['action' => 'queryCache'],
        );

        $moduleTemplate->assign(
            'renderedInfoBoxes',
            $this->getPageByType('page.query_cache')->getRenderedInfoBoxes(),
        );

        return $moduleTemplate->renderResponse('MySqlReport/Information');
    }

    private function miscAction(ModuleTemplate $moduleTemplate): ResponseInterface
    {
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'system_mysqlreport',
            'MySqlReport Misc',
            ['action' => 'misc'],
        );

        $moduleTemplate->assign(
            'renderedInfoBoxes',
            $this->getPageByType('page.misc')->getRenderedInfoBoxes(),
        );

        return $moduleTemplate->renderResponse('MySqlReport/Information');
    }

    private function getPageByType(string $type): Page
    {
        return $this->serviceLocator->get($type);
    }
}
