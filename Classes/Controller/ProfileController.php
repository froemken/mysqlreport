<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Controller;

use StefanFroemken\Mysqlreport\Domain\Repository\ProfileRepository;
use StefanFroemken\Mysqlreport\Helper\DownloadHelper;
use StefanFroemken\Mysqlreport\Helper\ModuleTemplateHelper;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Controller to show and analyze all queries of a request
 */
class ProfileController extends ActionController
{
    /**
     * @var BackendTemplateView
     */
    protected $view;

    /**
     * @var BackendTemplateView
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    /**
     * @var ProfileRepository
     */
    protected $profileRepository;

    /**
     * @var DownloadHelper
     */
    protected $downloadHelper;

    /**
     * @var ModuleTemplateHelper
     */
    protected $moduleTemplateHelper;

    public function injectProfileRepository(ProfileRepository $profileRepository): void
    {
        $this->profileRepository = $profileRepository;
    }

    public function injectDownloadHelper(DownloadHelper $downloadHelper): void
    {
        $this->downloadHelper = $downloadHelper;
    }

    public function injectModuleTemplateHelper(ModuleTemplateHelper $moduleTemplateHelper): void
    {
        $this->moduleTemplateHelper = $moduleTemplateHelper;
    }

    public function initializeAction(): void
    {
        $this->moduleTemplateHelper->setRequest($this->request);
    }

    public function listAction(): void
    {
        $this->view->assign('profileRecords', $this->profileRepository->findProfileRecordsForCall());
    }

    public function showAction(string $uniqueIdentifier): void
    {
        $buttonBar = $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();
        $this->moduleTemplateHelper->addOverviewButton($buttonBar);
        $this->moduleTemplateHelper->addShortcutButton($buttonBar);

        $this->view->assign('uniqueIdentifier', $uniqueIdentifier);
        $this->view->assign('profileTypes', $this->profileRepository->getProfileRecordsByUniqueIdentifier($uniqueIdentifier));
    }

    public function queryTypeAction(string $uniqueIdentifier, string $queryType): void
    {
        $buttonBar = $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();
        $this->moduleTemplateHelper->addOverviewButton($buttonBar);
        $this->moduleTemplateHelper->addShortcutButton($buttonBar);

        $this->view->assign('uniqueIdentifier', $uniqueIdentifier);
        $this->view->assign('queryType', $queryType);
        $this->view->assign('profileRecords', $this->profileRepository->getProfileRecordsByQueryType($uniqueIdentifier, $queryType));
    }

    public function profileInfoAction(int $uid): void
    {
        $buttonBar = $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();
        $this->moduleTemplateHelper->addOverviewButton($buttonBar);
        $this->moduleTemplateHelper->addShortcutButton($buttonBar);

        $profileRecord = $this->profileRepository->getProfileRecordByUid($uid);
        $profileRecord['profile'] = unserialize($profileRecord['profile'], ['allowed_classes' => false]);
        $profileRecord['explain'] = unserialize($profileRecord['explain_query'], ['allowed_classes' => false]);

        $this->view->assign('profileRecord', $profileRecord);
    }

    public function downloadAction(string $uniqueIdentifier, string $downloadType): void
    {
        if (empty($downloadType)) {
            throw new \RuntimeException('downloadType was not given in request', 1673904554);
        }
        if (!in_array($downloadType, ['csv', 'json'], true)) {
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
            $uniqueIdentifier,
            array_keys($columns)
        );

        if ($downloadType === 'csv') {
            $this->downloadAsCsv(array_values($columns), $records);
        }

        $this->downloadAsJson($records);
    }

    private function downloadAsCsv($headerColumns, array $records): void
    {
        $this->downloadHelper->asCSV($headerColumns, $records);
    }

    private function downloadAsJson(array $records): void
    {
        $this->downloadHelper->asJSON($records);
    }
}
