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
use StefanFroemken\Mysqlreport\Domain\Repository\QueryInformationRepository;
use StefanFroemken\Mysqlreport\Helper\DownloadHelper;
use StefanFroemken\Mysqlreport\Helper\ModuleTemplateHelper;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;

/**
 * Controller to show and analyze all queries of a request
 */
readonly class ProfileController
{
    public function __construct(
        private QueryInformationRepository $queryInformationRepository,
        private ModuleTemplateFactory $moduleTemplateFactory,
        private ModuleTemplateHelper $moduleTemplateHelper,
        private DownloadHelper $downloadHelper,
    ) {}

    public function listAction(ServerRequestInterface $request): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $this->moduleTemplateHelper->addOverviewButton($moduleTemplate->getDocHeaderComponent()->getButtonBar());
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'mysqlreport_profile_list',
            'MySqlReport Profiles',
        );

        $moduleTemplate->assign('queryInformationRecords', $this->queryInformationRepository->findQueryInformationRecordsForCall());

        return $moduleTemplate->renderResponse('Profile/List');
    }

    public function showAction(ServerRequestInterface $request): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $this->moduleTemplateHelper->addOverviewButton($moduleTemplate->getDocHeaderComponent()->getButtonBar());
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'mysqlreport_profile_show',
            'MySqlReport Show Profile',
        );

        $queryParameters = $request->getQueryParams();
        $uniqueIdentifier = $queryParameters['uniqueIdentifier'] ?? '';

        $moduleTemplate->assignMultiple([
            'uniqueIdentifier' => $uniqueIdentifier,
            'profileTypes' => $this->queryInformationRepository->getQueryInformationRecordsByUniqueIdentifier($uniqueIdentifier),
        ]);

        return $moduleTemplate->renderResponse('Profile/Show');
    }

    public function queryTypeAction(ServerRequestInterface $request): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $this->moduleTemplateHelper->addOverviewButton($moduleTemplate->getDocHeaderComponent()->getButtonBar());
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'mysqlreport_profile_querytype',
            'MySqlReport Show Profile',
        );

        $queryParameters = $request->getQueryParams();
        $uniqueIdentifier = $queryParameters['uniqueIdentifier'] ?? '';
        $queryType = $queryParameters['queryType'] ?? '';

        $moduleTemplate->assignMultiple([
            'uniqueIdentifier' => $uniqueIdentifier,
            'queryType' => $queryType,
            'queryInformationRecords' => $this->queryInformationRepository->getQueryInformationRecordsByQueryType($uniqueIdentifier, $queryType),
        ]);

        return $moduleTemplate->renderResponse('Profile/QueryType');
    }

    public function infoAction(ServerRequestInterface $request): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $this->moduleTemplateHelper->addOverviewButton($moduleTemplate->getDocHeaderComponent()->getButtonBar());
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'mysqlreport_profile_info',
            'MySqlReport Show Query Information',
        );

        $queryParameters = $request->getQueryParams();
        $uid = (int)($queryParameters['uid'] ?? 0);

        $queryInformationRecord = $this->queryInformationRepository->getQueryInformationRecordByUid($uid);
        $queryInformationRecord['explain'] = unserialize($queryInformationRecord['explain_query'], ['allowed_classes' => false]);

        $moduleTemplate->assign('queryInformationRecord', $queryInformationRecord);

        return $moduleTemplate->renderResponse('Profile/Info');
    }

    public function profilingAction(ServerRequestInterface $request): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $this->moduleTemplateHelper->addOverviewButton($moduleTemplate->getDocHeaderComponent()->getButtonBar());
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'mysqlreport_profile_profiling',
            'MySqlReport Show Query Profiling',
        );

        $queryParameters = $request->getQueryParams();
        $queryInformationRecord = $this->queryInformationRepository->getQueryInformationRecordByUid((int)($queryParameters['uid'] ?? 0));

        $moduleTemplate->assign('queryInformationRecord', $queryInformationRecord);
        $moduleTemplate->assign('profiling', $this->queryInformationRepository->getQueryProfiling($queryInformationRecord));

        return $moduleTemplate->renderResponse('Profile/QueryProfiling');
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
            'query' => 'Query',
        ];

        $records = $this->queryInformationRepository->getQueryInformationRecordsForDownloadByUniqueIdentifier(
            $queryParameters['uniqueIdentifier'] ?? '',
            array_keys($columns),
        );

        if ($queryParameters['downloadType'] === 'csv') {
            return $this->downloadAsCsv(array_values($columns), $records);
        }

        return $this->downloadAsJson($records);
    }

    /**
     * @param array<string> $headerColumns
     * @param array<mixed> $records
     * @return ResponseInterface
     */
    private function downloadAsCsv(array $headerColumns, array $records): ResponseInterface
    {
        return $this->downloadHelper->asCSV($headerColumns, $records);
    }

    /**
     * @param array<mixed> $records
     * @return ResponseInterface
     */
    private function downloadAsJson(array $records): ResponseInterface
    {
        return $this->downloadHelper->asJSON($records);
    }
}
