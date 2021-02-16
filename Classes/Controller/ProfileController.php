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

    public function injectDatabaseRepository(DatabaseRepository $databaseRepository)
    {
        $this->databaseRepository = $databaseRepository;
    }

    /**
     * list action
     */
    public function listAction()
    {
        $this->view->assign('profiles', $this->databaseRepository->findProfilingsForCall());
    }

    /**
     * show action
     *
     * @param string $uniqueIdentifier
     */
    public function showAction($uniqueIdentifier)
    {
        $this->view->assign('profileTypes', $this->databaseRepository->getProfilingByUniqueIdentifier($uniqueIdentifier));
    }

    /**
     * query type action
     *
     * @param string $uniqueIdentifier
     * @param string $queryType
     */
    public function queryTypeAction($uniqueIdentifier, $queryType)
    {
        $this->view->assign('uniqueIdentifier', $uniqueIdentifier);
        $this->view->assign('queryType', $queryType);
        $this->view->assign('profilings', $this->databaseRepository->getProfilingsByQueryType($uniqueIdentifier, $queryType));
    }

    /**
     * profileInfo action
     *
     * @param string $uniqueIdentifier
     * @param string $queryType
     * @param int $uid
     */
    public function profileInfoAction($uniqueIdentifier, $queryType, $uid)
    {
        $this->view->assign('uniqueIdentifier', $uniqueIdentifier);
        $this->view->assign('queryType', $queryType);
        $profiling = $this->databaseRepository->getProfilingByUid($uid);
        $profiling['profile'] = unserialize($profiling['profile']);
        $profiling['explain'] = unserialize($profiling['explain_query']);
        $this->view->assign('profiling', $profiling);
    }
}
