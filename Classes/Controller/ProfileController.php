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
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;

/**
 * Controller to show and analyze all queries of a request
 */
#[AsController]
final readonly class ProfileController
{
    public function __construct(
        private QueryInformationRepository $queryInformationRepository,
        private ModuleTemplateFactory $moduleTemplateFactory,
        private DownloadHelper $downloadHelper,
    ) {}

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        $action = (string)($request->getQueryParams()['action'] ?? 'list');
        return match ($action) {
            'show' => $this->showAction($request),
            'queryType' => $this->queryTypeAction($request),
            'info' => $this->infoAction($request),
            'queryProfiling' => $this->queryProfilingAction($request),
            'download' => $this->downloadAction($request),
            default => $this->listAction($request),
        };
    }

    public function listAction(ServerRequestInterface $request): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $moduleTemplate->getDocHeaderComponent()->setShortcutContext(
            'mysqlreport_profile',
            'MySQL Report - List Profiles',
        );

        $moduleTemplate->assign(
            'queryInformationRecords',
            $this->queryInformationRepository->findQueryInformationRecordsForCall(),
        );

        return $moduleTemplate->renderResponse('Profile/List');
    }

    public function showAction(ServerRequestInterface $request): ResponseInterface
    {
        $uniqueIdentifier = (string)($request->getQueryParams()['uniqueIdentifier'] ?? '');
        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $moduleTemplate->getDocHeaderComponent()->setShortcutContext(
            'mysqlreport_profile',
            'MySQL Report - Show Profile',
        );

        $moduleTemplate->assignMultiple([
            'uniqueIdentifier' => $uniqueIdentifier,
            'profileTypes' => $this->queryInformationRepository->getQueryInformationRecordsByUniqueIdentifier(
                $uniqueIdentifier,
            ),
        ]);

        return $moduleTemplate->renderResponse('Profile/Show');
    }

    public function queryTypeAction(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $uniqueIdentifier = (string)($queryParams['uniqueIdentifier'] ?? '');
        $queryType = (string)($queryParams['queryType'] ?? '');

        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $moduleTemplate->getDocHeaderComponent()->setShortcutContext(
            'mysqlreport_profile',
            'MySQL Report - Show Query Type',
        );

        $moduleTemplate->assignMultiple([
            'uniqueIdentifier' => $uniqueIdentifier,
            'queryType' => $queryType,
            'queryInformationRecords' => $this->queryInformationRepository->getQueryInformationRecordsByQueryType(
                $uniqueIdentifier,
                $queryType,
            ),
        ]);

        return $moduleTemplate->renderResponse('Profile/QueryType');
    }

    public function infoAction(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $uid = (int)($queryParams['uid'] ?? 0);
        $prevRoute = $this->resolvePrevRoute($queryParams);

        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $moduleTemplate->getDocHeaderComponent()->setShortcutContext(
            'mysqlreport_profile',
            'MySQL Report - Show Query Information',
        );

        $queryInformationRecord = $this->queryInformationRepository->getQueryInformationRecordByUid($uid);
        $queryInformationRecord['explain'] = unserialize($queryInformationRecord['explain_query'], ['allowed_classes' => false]);

        $moduleTemplate->assignMultiple([
            'queryInformationRecord' => $queryInformationRecord,
            'prevRoute' => $prevRoute,
            'prevController' => $prevRoute,
        ]);

        return $moduleTemplate->renderResponse('Profile/Info');
    }

    public function queryProfilingAction(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $uid = (int)($queryParams['uid'] ?? 0);
        $prevRoute = $this->resolvePrevRoute($queryParams);

        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $moduleTemplate->getDocHeaderComponent()->setShortcutContext(
            'mysqlreport_profile',
            'MySQL Report - Show Query Profiling',
        );

        $record = $this->queryInformationRepository->getQueryInformationRecordByUid($uid);
        $moduleTemplate->assignMultiple([
            'queryInformationRecord' => $record,
            'profiling' => $this->queryInformationRepository->getQueryProfiling($record),
            'prevRoute' => $prevRoute,
            'prevController' => $prevRoute,
        ]);

        return $moduleTemplate->renderResponse('Profile/QueryProfiling');
    }

    public function downloadAction(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $uniqueIdentifier = (string)($queryParams['uniqueIdentifier'] ?? '');
        $downloadType = (string)($queryParams['downloadType'] ?? '');

        if ($downloadType === '') {
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
            'query' => 'Query',
        ];

        $records = $this->queryInformationRepository->getQueryInformationRecordsForDownloadByUniqueIdentifier(
            $uniqueIdentifier,
            array_keys($columns),
        );

        if ($downloadType === 'csv') {
            return $this->downloadHelper->asCSV(array_values($columns), $records);
        }
        return $this->downloadHelper->asJSON($records);
    }

    /**
     * @param array<string, mixed> $queryParams
     */
    private function resolvePrevRoute(array $queryParams): string
    {
        $prev = (string)($queryParams['prevRoute'] ?? $queryParams['prevController'] ?? '');
        return match ($prev) {
            'FileSort' => 'mysqlreport_filesort',
            'FullTableScan' => 'mysqlreport_fulltablescan',
            'SlowQuery' => 'mysqlreport_slowquery',
            default => $prev,
        };
    }
}
