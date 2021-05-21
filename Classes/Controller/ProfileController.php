<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Controller;

use StefanFroemken\Mysqlreport\Domain\Repository\DatabaseRepository;
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
     * @var DatabaseRepository
     */
    protected $databaseRepository;

    public function __construct(DatabaseRepository $databaseRepository)
    {
        $this->databaseRepository = $databaseRepository;
    }

    public function listAction(): void
    {
        $this->view->assign('profiles', $this->databaseRepository->findProfilingsForCall());
    }

    /**
     * @param string $uniqueIdentifier
     */
    public function showAction(string $uniqueIdentifier): void
    {
        $this->view->assign('profileTypes', $this->databaseRepository->getProfilingByUniqueIdentifier($uniqueIdentifier));
    }

    /**
     * @param string $uniqueIdentifier
     * @param string $queryType
     */
    public function queryTypeAction(string $uniqueIdentifier, string $queryType): void
    {
        $this->view->assign('uniqueIdentifier', $uniqueIdentifier);
        $this->view->assign('queryType', $queryType);
        $this->view->assign('profilings', $this->databaseRepository->getProfilingsByQueryType($uniqueIdentifier, $queryType));
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
        $profiling = $this->databaseRepository->getProfilingByUid($uid);
        $profiling['profile'] = unserialize($profiling['profile']);
        $profiling['explain'] = unserialize($profiling['explain_query']);
        $this->view->assign('profiling', $profiling);
    }
}
