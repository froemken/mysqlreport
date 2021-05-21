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

    public function __construct(StatusRepository $statusRepository, VariablesRepository $variablesRepository)
    {
        $this->statusRepository = $statusRepository;
        $this->variablesRepository = $variablesRepository;
    }

    public function indexAction(): void
    {
        $this->view->assign('status', $this->statusRepository->findAll());
        $this->view->assign('variables', $this->variablesRepository->findAll());
    }

    public function queryCacheAction(): void
    {
        $this->view->assign('status', $this->statusRepository->findAll());
        $this->view->assign('variables', $this->variablesRepository->findAll());
    }

    public function innoDbBufferAction(): void
    {
        $this->view->assign('status', $this->statusRepository->findAll());
        $this->view->assign('variables', $this->variablesRepository->findAll());
    }

    public function threadCacheAction(): void
    {
        $this->view->assign('status', $this->statusRepository->findAll());
        $this->view->assign('variables', $this->variablesRepository->findAll());
    }

    public function tableCacheAction(): void
    {
        $this->view->assign('status', $this->statusRepository->findAll());
        $this->view->assign('variables', $this->variablesRepository->findAll());
    }
}
