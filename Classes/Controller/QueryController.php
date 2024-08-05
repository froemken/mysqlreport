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
        ModuleTemplateHelper $moduleTemplateHelper,
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
            'MySqlReport Filesort',
        );

        $moduleTemplate->assign('profileRecords', $this->profileRepository->findProfileRecordsWithFilesort());

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

        $moduleTemplate->assign('profileRecords', $this->profileRepository->findProfileRecordsWithFullTableScan());

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

        $moduleTemplate->assign('profileRecords', $this->profileRepository->findProfileRecordsWithSlowQueries());
        $moduleTemplate->assign('slowQueryTime', $this->extConf->getSlowQueryTime());

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

        $profileRecord = $this->profileRepository->getProfileRecordByUid((int)($queryParameters['uid'] ?? 0));
        $profileRecord['profile'] = unserialize($profileRecord['profile'], ['allowed_classes' => false]);
        $profileRecord['explain'] = unserialize($profileRecord['explain_query'], ['allowed_classes' => false]);

        $moduleTemplate->assign('profileRecord', $profileRecord);
        $moduleTemplate->assign('prevRouteIdentifier', $queryParameters['prevRouteIdentifier'] ?? '');

        return $moduleTemplate->renderResponse('Query/ProfileInfo');
    }
}
