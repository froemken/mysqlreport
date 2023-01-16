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
use StefanFroemken\Mysqlreport\Helper\DownloadHelper;
use StefanFroemken\Mysqlreport\Helper\ModuleTemplateHelper;
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

    private DownloadHelper $downloadHelper;

    public function __construct(
        ProfileRepository $profileRepository,
        ModuleTemplateFactory $moduleTemplateFactory,
        ModuleTemplateHelper $moduleTemplateHelper,
        DownloadHelper $downloadHelper
    ) {
        $this->profileRepository = $profileRepository;
        $this->moduleTemplateFactory = $moduleTemplateFactory;
        $this->moduleTemplateHelper = $moduleTemplateHelper;
        $this->downloadHelper = $downloadHelper;
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
        $uniqueIdentifier = $queryParameters['uniqueIdentifier'] ?? '';

        $view->assignMultiple([
            'uniqueIdentifier' => $uniqueIdentifier,
            'profileTypes' => $this->profileRepository->getProfileRecordsByUniqueIdentifier($uniqueIdentifier)
        ]);

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

    public function downloadAction(ServerRequestInterface $request): ResponseInterface
    {
        $queryParameters = $request->getQueryParams();

        if (empty($queryParameters['downloadType'])) {
            throw new \RuntimeException('downloadType was not given in request', 1673904554);
        }
        if (!in_array($queryParameters['downloadType'], ['csv', 'json'], true)) {
            throw new \RuntimeException('Given downloadType is not allowed', 1673904777);
        }

        $columns = [
            'uid' => 'UID',
            'query_type' => 'Query Type',
            'unique_call_identifier' => 'Request ID',
            'request' => 'HTTP Request',
            'query_id' => 'Query ID',
            'query' => 'Query'
        ];

        $records = $this->profileRepository->getProfileRecordsForDownloadByUniqueIdentifier(
            $queryParameters['uniqueIdentifier'] ?? '',
            array_keys($columns)
        );

        if ($queryParameters['downloadType'] === 'csv') {
            return $this->downloadAsCsv(array_values($columns), $records);
        }

        return $this->downloadAsJson($records);
    }

    private function downloadAsCsv($headerColumns, array $records): ResponseInterface
    {
        return $this->downloadHelper->asCSV($headerColumns, $records);
    }

    private function downloadAsJson(array $records): ResponseInterface
    {
        return $this->downloadHelper->asJSON($records);
    }
}
