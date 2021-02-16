<?php
namespace StefanFroemken\Mysqlreport\Controller;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
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

    /**
     * inject databaseRepository
     *
     * @param DatabaseRepository $databaseRepository
     * @return void
     */
    public function injectDatabaseRepository(DatabaseRepository $databaseRepository)
    {
        $this->databaseRepository = $databaseRepository;
    }

    /**
     * list action
     *
     * @return void
     */
    public function listAction()
    {
        $this->view->assign('profiles', $this->databaseRepository->findProfilingsForCall());
    }

    /**
     * show action
     *
     * @param string $uniqueIdentifier
     *
     * @return void
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
     *
     * @return void
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
     * @param integer $uid
     *
     * @return void
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
