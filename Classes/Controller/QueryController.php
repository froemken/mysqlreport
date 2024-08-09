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
use StefanFroemken\Mysqlreport\Domain\Repository\QueryInformationRepository;
use StefanFroemken\Mysqlreport\Helper\ModuleTemplateHelper;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;

/**
 * Controller to show results of FTS and filesort
 */
readonly class QueryController
{
    public function __construct(
        private QueryInformationRepository $queryInformationRepository,
        private ExtConf $extConf,
        private ModuleTemplateFactory $moduleTemplateFactory,
        private ModuleTemplateHelper $moduleTemplateHelper,
    ) {}

    public function filesortAction(ServerRequestInterface $request): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $this->moduleTemplateHelper->addOverviewButton($moduleTemplate->getDocHeaderComponent()->getButtonBar());
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'mysqlreport_query_filesort',
            'MySqlReport Filesort',
        );

        $moduleTemplate->assign('queryInformationRecords', $this->queryInformationRepository->findQueryInformationRecordsWithFilesort());

        return $moduleTemplate->renderResponse('Query/Filesort');
    }

    public function fullTableScanAction(ServerRequestInterface $request): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $this->moduleTemplateHelper->addOverviewButton($moduleTemplate->getDocHeaderComponent()->getButtonBar());
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'mysqlreport_query_fulltablescan',
            'MySqlReport Full Table Scan',
        );

        $moduleTemplate->assign('queryInformationRecords', $this->queryInformationRepository->findQueryInformationRecordsWithFullTableScan());

        return $moduleTemplate->renderResponse('Query/FullTableScan');
    }

    public function slowQueryAction(ServerRequestInterface $request): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $this->moduleTemplateHelper->addOverviewButton($moduleTemplate->getDocHeaderComponent()->getButtonBar());
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'mysqlreport_query_slowquery',
            'MySqlReport Slow Query',
        );

        $moduleTemplate->assign('queryInformationRecords', $this->queryInformationRepository->findQueryInformationRecordsWithSlowQueries());
        $moduleTemplate->assign('slowQueryTime', $this->extConf->getSlowQueryThreshold());

        return $moduleTemplate->renderResponse('Query/SlowQuery');
    }

    public function profileInfoAction(ServerRequestInterface $request): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $this->moduleTemplateHelper->addOverviewButton($moduleTemplate->getDocHeaderComponent()->getButtonBar());
        $this->moduleTemplateHelper->addShortcutButton(
            $moduleTemplate->getDocHeaderComponent()->getButtonBar(),
            'mysqlreport_query_profileinfo',
            'MySqlReport Profile Info',
        );

        $queryParameters = $request->getQueryParams();

        $queryInformationRecord = $this->queryInformationRepository->getQueryInformationRecordByUid((int)($queryParameters['uid'] ?? 0));
        $queryInformationRecord['explain'] = unserialize($queryInformationRecord['explain_query'], ['allowed_classes' => false]);

        $moduleTemplate->assign('queryInformationRecord', $queryInformationRecord);
        $moduleTemplate->assign('profiling', $this->queryInformationRepository->getQueryProfiling($queryInformationRecord));
        $moduleTemplate->assign('prevRouteIdentifier', $queryParameters['prevRouteIdentifier'] ?? '');

        return $moduleTemplate->renderResponse('Query/ProfileInfo');
    }
}
