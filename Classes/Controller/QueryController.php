<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use StefanFroemken\Mysqlreport\Configuration\ExtConf;
use StefanFroemken\Mysqlreport\Domain\Repository\ProfileRepository;
use StefanFroemken\Mysqlreport\Helper\ModuleTemplateHelper;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Controller to show results of FTS and filesort
 */
class QueryController
{
    protected ProfileRepository $profileRepository;

    protected ExtConf $extConf;

    private ModuleTemplateFactory $moduleTemplateFactory;

    private ModuleTemplateHelper $moduleTemplateHelper;

    public function __construct(
        ProfileRepository $profileRepository,
        ExtConf $extConf,
        ModuleTemplateFactory $moduleTemplateFactory,
        ModuleTemplateHelper $moduleTemplateHelper
    ) {
        $this->profileRepository = $profileRepository;
        $this->extConf = $extConf;
        $this->moduleTemplateFactory = $moduleTemplateFactory;
        $this->moduleTemplateHelper = $moduleTemplateHelper;
    }

    public function filesortAction(ServerRequestInterface $request): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $this->moduleTemplateHelper->addOverviewButton($moduleTemplate->getDocHeaderComponent()->getButtonBar());
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'mysqlreport_query_filesort',
            'MySqlReport Filesort'
        );

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplateRootPaths(['EXT:mysqlreport/Resources/Private/Templates']);
        $view->setPartialRootPaths(['EXT:mysqlreport/Resources/Private/Partials']);
        $view->setLayoutRootPaths(['EXT:mysqlreport/Resources/Private/Layouts']);
        $view->setTemplate('Query/Filesort');

        $view->assign('profileRecords', $this->profileRepository->findProfileRecordsWithFilesort());

        $moduleTemplate->setContent($view->render());

        return new HtmlResponse($moduleTemplate->renderContent());
    }

    public function fullTableScanAction(ServerRequestInterface $request): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $this->moduleTemplateHelper->addOverviewButton($moduleTemplate->getDocHeaderComponent()->getButtonBar());
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'mysqlreport_query_fulltablescan',
            'MySqlReport Full Table Scan'
        );

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplateRootPaths(['EXT:mysqlreport/Resources/Private/Templates']);
        $view->setPartialRootPaths(['EXT:mysqlreport/Resources/Private/Partials']);
        $view->setLayoutRootPaths(['EXT:mysqlreport/Resources/Private/Layouts']);
        $view->setTemplate('Query/FullTableScan');

        $view->assign('profileRecords', $this->profileRepository->findProfileRecordsWithFullTableScan());

        $moduleTemplate->setContent($view->render());

        return new HtmlResponse($moduleTemplate->renderContent());
    }

    public function slowQueryAction(ServerRequestInterface $request): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $this->moduleTemplateHelper->addOverviewButton($moduleTemplate->getDocHeaderComponent()->getButtonBar());
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'mysqlreport_query_slowquery',
            'MySqlReport Slow Query'
        );

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplateRootPaths(['EXT:mysqlreport/Resources/Private/Templates']);
        $view->setPartialRootPaths(['EXT:mysqlreport/Resources/Private/Partials']);
        $view->setLayoutRootPaths(['EXT:mysqlreport/Resources/Private/Layouts']);
        $view->setTemplate('Query/SlowQuery');

        $view->assign('profileRecords', $this->profileRepository->findProfileRecordsWithSlowQueries());
        $view->assign('slowQueryTime', $this->extConf->getSlowQueryTime());

        $moduleTemplate->setContent($view->render());

        return new HtmlResponse($moduleTemplate->renderContent());
    }

    public function profileInfoAction(ServerRequestInterface $request): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $this->moduleTemplateHelper->addOverviewButton($moduleTemplate->getDocHeaderComponent()->getButtonBar());
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'mysqlreport_query_profileinfo',
            'MySqlReport Profile Info'
        );

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplateRootPaths(['EXT:mysqlreport/Resources/Private/Templates']);
        $view->setPartialRootPaths(['EXT:mysqlreport/Resources/Private/Partials']);
        $view->setLayoutRootPaths(['EXT:mysqlreport/Resources/Private/Layouts']);
        $view->setTemplate('Query/ProfileInfo');

        $queryParameters = $request->getQueryParams();

        $profileRecord = $this->profileRepository->getProfileRecordByUid((int)($queryParameters['uid'] ?? 0));
        $profileRecord['profile'] = unserialize($profileRecord['profile'], ['allowed_classes' => false]);
        $profileRecord['explain'] = unserialize($profileRecord['explain_query'], ['allowed_classes' => false]);

        $view->assign('profileRecord', $profileRecord);
        $view->assign('prevRouteIdentifier', $queryParameters['prevRouteIdentifier'] ?? '');

        $moduleTemplate->setContent($view->render());

        return new HtmlResponse($moduleTemplate->renderContent());
    }
}
