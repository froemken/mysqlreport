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
use StefanFroemken\Mysqlreport\Domain\Repository\ProfileRepository;
use StefanFroemken\Mysqlreport\Helper\ModuleTemplateHelper;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Controller to show and analyze all queries of a request
 */
class ProfileController
{
    private ProfileRepository $profileRepository;

    private ModuleTemplateFactory $moduleTemplateFactory;

    private ModuleTemplateHelper $moduleTemplateHelper;

    public function __construct(
        ProfileRepository $profileRepository,
        ModuleTemplateFactory $moduleTemplateFactory,
        ModuleTemplateHelper $moduleTemplateHelper
    ) {
        $this->profileRepository = $profileRepository;
        $this->moduleTemplateFactory = $moduleTemplateFactory;
        $this->moduleTemplateHelper = $moduleTemplateHelper;
    }

    public function listAction(ServerRequestInterface $request): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $this->moduleTemplateHelper->addOverviewButton($moduleTemplate->getDocHeaderComponent()->getButtonBar());
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'mysqlreport_profile_list',
            'MySqlReport Profiles'
        );

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplateRootPaths(['EXT:mysqlreport/Resources/Private/Templates']);
        $view->setPartialRootPaths(['EXT:mysqlreport/Resources/Private/Partials']);
        $view->setLayoutRootPaths(['EXT:mysqlreport/Resources/Private/Layouts']);
        $view->setTemplate('Profile/List');

        $view->assign('profileRecords', $this->profileRepository->findProfileRecordsForCall());

        $moduleTemplate->setContent($view->render());

        return new HtmlResponse($moduleTemplate->renderContent());
    }

    public function showAction(ServerRequestInterface $request): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $this->moduleTemplateHelper->addOverviewButton($moduleTemplate->getDocHeaderComponent()->getButtonBar());
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'mysqlreport_profile_show',
            'MySqlReport Show Profile'
        );

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplateRootPaths(['EXT:mysqlreport/Resources/Private/Templates']);
        $view->setPartialRootPaths(['EXT:mysqlreport/Resources/Private/Partials']);
        $view->setLayoutRootPaths(['EXT:mysqlreport/Resources/Private/Layouts']);
        $view->setTemplate('Profile/Show');

        $queryParameters = $request->getQueryParams();
        $view->assign(
            'profileTypes',
            $this->profileRepository->getProfileRecordsByUniqueIdentifier(
                $queryParameters['uniqueIdentifier'] ?? ''
            )
        );

        $moduleTemplate->setContent($view->render());

        return new HtmlResponse($moduleTemplate->renderContent());
    }

    public function queryTypeAction(ServerRequestInterface $request): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $this->moduleTemplateHelper->addOverviewButton($moduleTemplate->getDocHeaderComponent()->getButtonBar());
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'mysqlreport_profile_querytype',
            'MySqlReport Show Profile'
        );

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplateRootPaths(['EXT:mysqlreport/Resources/Private/Templates']);
        $view->setPartialRootPaths(['EXT:mysqlreport/Resources/Private/Partials']);
        $view->setLayoutRootPaths(['EXT:mysqlreport/Resources/Private/Layouts']);
        $view->setTemplate('Profile/QueryType');

        $queryParameters = $request->getQueryParams();
        $uniqueIdentifier = $queryParameters['uniqueIdentifier'] ?? '';
        $queryType = $queryParameters['queryType'] ?? '';

        $view->assignMultiple([
            'uniqueIdentifier' => $uniqueIdentifier,
            'queryType' => $queryType,
            'profileRecords' => $this->profileRepository->getProfileRecordsByQueryType($uniqueIdentifier, $queryType),
        ]);

        $moduleTemplate->setContent($view->render());

        return new HtmlResponse($moduleTemplate->renderContent());
    }

    public function infoAction(ServerRequestInterface $request): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $this->moduleTemplateHelper->addOverviewButton($moduleTemplate->getDocHeaderComponent()->getButtonBar());
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'mysqlreport_profile_info',
            'MySqlReport Show Profile'
        );

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplateRootPaths(['EXT:mysqlreport/Resources/Private/Templates']);
        $view->setPartialRootPaths(['EXT:mysqlreport/Resources/Private/Partials']);
        $view->setLayoutRootPaths(['EXT:mysqlreport/Resources/Private/Layouts']);
        $view->setTemplate('Profile/Info');

        $queryParameters = $request->getQueryParams();
        $uid = (int)($queryParameters['uid'] ?? 0);

        $profileRecord = $this->profileRepository->getProfileRecordByUid($uid);
        $profileRecord['profile'] = unserialize($profileRecord['profile'], ['allowed_classes' => false]);
        $profileRecord['explain'] = unserialize($profileRecord['explain_query'], ['allowed_classes' => false]);

        $view->assign('profileRecord', $profileRecord);

        $moduleTemplate->setContent($view->render());

        return new HtmlResponse($moduleTemplate->renderContent());
    }
}
