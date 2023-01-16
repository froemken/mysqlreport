<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Controller;

use StefanFroemken\Mysqlreport\Configuration\ExtConf;
use StefanFroemken\Mysqlreport\Domain\Repository\ProfileRepository;
use StefanFroemken\Mysqlreport\Helper\ModuleTemplateHelper;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Controller to show results of FTS and filesort
 */
class QueryController extends ActionController
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
     * @var ModuleTemplateHelper
     */
    protected $moduleTemplateHelper;

    /**
     * @var ExtConf
     */
    protected $extConf;

    public function injectProfileRepository(ProfileRepository $profileRepository): void
    {
        $this->profileRepository = $profileRepository;
    }

    public function injectModuleTemplateHelper(ModuleTemplateHelper $moduleTemplateHelper): void
    {
        $this->moduleTemplateHelper = $moduleTemplateHelper;
    }

    public function injectExtConf(ExtConf $extConf): void
    {
        $this->extConf = $extConf;
    }

    public function initializeAction(): void
    {
        $this->moduleTemplateHelper->setRequest($this->request);
    }

    public function filesortAction(): void
    {
        $buttonBar = $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();
        $this->moduleTemplateHelper->addOverviewButton($buttonBar);
        $this->moduleTemplateHelper->addShortcutButton($buttonBar);

        $this->view->assign('profileRecords', $this->profileRepository->findProfileRecordsWithFilesort());
    }

    public function fullTableScanAction(): void
    {
        $buttonBar = $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();
        $this->moduleTemplateHelper->addOverviewButton($buttonBar);
        $this->moduleTemplateHelper->addShortcutButton($buttonBar);

        $this->view->assign('profileRecords', $this->profileRepository->findProfileRecordsWithFullTableScan());
    }

    public function slowQueryAction(): void
    {
        $buttonBar = $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();
        $this->moduleTemplateHelper->addOverviewButton($buttonBar);
        $this->moduleTemplateHelper->addShortcutButton($buttonBar);

        $this->view->assign('profileRecords', $this->profileRepository->findProfileRecordsWithSlowQueries());
        $this->view->assign('slowQueryTime', $this->extConf->getSlowQueryTime());
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
}
