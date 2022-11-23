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
use StefanFroemken\Mysqlreport\Configuration\ExtConf;
use StefanFroemken\Mysqlreport\Domain\Repository\ProfileRepository;

/**
 * Controller to show results of FTS and filesort
 */
class QueryController extends AbstractController
{
    /**
     * @var ProfileRepository
     */
    protected $profileRepository;

    /**
     * @var ExtConf
     */
    protected $extConf;

    public function injectProfileRepository(ProfileRepository $profileRepository): void
    {
        $this->profileRepository = $profileRepository;
    }

    public function injectExtConf(ExtConf $extConf): void
    {
        $this->extConf = $extConf;
    }

    public function filesortAction(): ResponseInterface
    {
        $this->view->assign('profileRecords', $this->profileRepository->findProfileRecordsWithFilesort());

        $moduleTemplate = $this->getModuleTemplate();
        $moduleTemplate->setContent($this->view->render());

        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    public function fullTableScanAction(): ResponseInterface
    {
        $this->view->assign('profileRecords', $this->profileRepository->findProfileRecordsWithFullTableScan());

        $moduleTemplate = $this->getModuleTemplate();
        $moduleTemplate->setContent($this->view->render());

        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    public function slowQueryAction(): ResponseInterface
    {
        $this->view->assign('profileRecords', $this->profileRepository->findProfileRecordsWithSlowQueries());
        $this->view->assign('slowQueryTime', $this->extConf->getSlowQueryTime());

        $moduleTemplate = $this->getModuleTemplate();
        $moduleTemplate->setContent($this->view->render());

        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    public function profileInfoAction(int $uid): ResponseInterface
    {
        $profileRecord = $this->profileRepository->getProfileRecordByUid($uid);
        $profileRecord['profile'] = unserialize($profileRecord['profile'], ['allowed_classes' => false]);
        $profileRecord['explain'] = unserialize($profileRecord['explain_query'], ['allowed_classes' => false]);

        $this->view->assign('profileRecord', $profileRecord);

        $moduleTemplate = $this->getModuleTemplate();
        $moduleTemplate->setContent($this->view->render());

        return $this->htmlResponse($moduleTemplate->renderContent());
    }
}
