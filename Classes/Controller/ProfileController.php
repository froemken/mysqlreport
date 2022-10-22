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
use TYPO3\CMS\Backend\View\BackendTemplateView;

/**
 * Controller to show and analyze all queries of a request
 */
class ProfileController extends AbstractController
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

    public function __construct(ProfileRepository $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    public function listAction(): void
    {
        $this->view->assign('profileRecords', $this->profileRepository->findProfileRecordsForCall());
    }

    public function showAction(string $uniqueIdentifier): void
    {
        $this->view->assign('profileTypes', $this->profileRepository->getProfileRecordsByUniqueIdentifier($uniqueIdentifier));
    }

    public function queryTypeAction(string $uniqueIdentifier, string $queryType): void
    {
        $this->view->assign('uniqueIdentifier', $uniqueIdentifier);
        $this->view->assign('queryType', $queryType);
        $this->view->assign('profileRecords', $this->profileRepository->getProfileRecordsByQueryType($uniqueIdentifier, $queryType));
    }

    public function profileInfoAction(int $uid): void
    {
        $profileRecord = $this->profileRepository->getProfileRecordByUid($uid);
        $profileRecord['profile'] = unserialize($profileRecord['profile'], ['allowed_classes' => false]);
        $profileRecord['explain'] = unserialize($profileRecord['explain_query'], ['allowed_classes' => false]);

        $this->view->assign('profileRecord', $profileRecord);
    }
}
