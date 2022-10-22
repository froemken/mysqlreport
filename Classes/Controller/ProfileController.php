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
        $this->view->assign('profiles', $this->profileRepository->findProfilingsForCall());
    }

    /**
     * @param string $uniqueIdentifier
     */
    public function showAction(string $uniqueIdentifier): void
    {
        $this->view->assign('profileTypes', $this->profileRepository->getProfilingByUniqueIdentifier($uniqueIdentifier));
    }

    /**
     * @param string $uniqueIdentifier
     * @param string $queryType
     */
    public function queryTypeAction(string $uniqueIdentifier, string $queryType): void
    {
        $this->view->assign('uniqueIdentifier', $uniqueIdentifier);
        $this->view->assign('queryType', $queryType);
        $this->view->assign('profilings', $this->profileRepository->getProfilingsByQueryType($uniqueIdentifier, $queryType));
    }

    /**
     * @param string $uniqueIdentifier
     * @param string $queryType
     * @param int $uid
     */
    public function profileInfoAction(string $uniqueIdentifier, string $queryType, int $uid): void
    {
        $this->view->assign('uniqueIdentifier', $uniqueIdentifier);
        $this->view->assign('queryType', $queryType);

        $profiling = $this->profileRepository->getProfilingByUid($uid);
        $profiling['profile'] = unserialize($profiling['profile'], ['allowed_classes' => false]);
        $profiling['explain'] = unserialize($profiling['explain_query'], ['allowed_classes' => false]);

        $this->view->assign('profiling', $profiling);
    }
}
