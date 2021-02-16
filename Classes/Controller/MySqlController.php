<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Controller;

use StefanFroemken\Mysqlreport\Domain\Repository\StatusRepository;
use StefanFroemken\Mysqlreport\Domain\Repository\VariablesRepository;
use TYPO3\CMS\Backend\View\BackendTemplateView;

/**
 * Controller to show a basic analysis of MySQL variables and status
 */
class MySqlController extends AbstractController
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
     * @var StatusRepository
     */
    protected $statusRepository;

    /**
     * @var VariablesRepository
     */
    protected $variablesRepository;

    public function injectStatusRepository(StatusRepository $statusRepository)
    {
        $this->statusRepository = $statusRepository;
    }

    public function injectVariablesRepository(VariablesRepository $variablesRepository)
    {
        $this->variablesRepository = $variablesRepository;
    }

    public function indexAction()
    {
        $this->view->assign('status', $this->statusRepository->findAll());
        $this->view->assign('variables', $this->variablesRepository->findAll());
    }

    public function queryCacheAction()
    {
        $this->view->assign('status', $this->statusRepository->findAll());
        $this->view->assign('variables', $this->variablesRepository->findAll());
    }

    public function innoDbBufferAction()
    {
        $this->view->assign('status', $this->statusRepository->findAll());
        $this->view->assign('variables', $this->variablesRepository->findAll());
    }

    public function threadCacheAction()
    {
        $this->view->assign('status', $this->statusRepository->findAll());
        $this->view->assign('variables', $this->variablesRepository->findAll());
    }

    public function tableCacheAction()
    {
        $this->view->assign('status', $this->statusRepository->findAll());
        $this->view->assign('variables', $this->variablesRepository->findAll());
    }
}
