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
use StefanFroemken\Mysqlreport\Domain\Repository\StatusRepository;
use StefanFroemken\Mysqlreport\Domain\Repository\VariablesRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * @package mysqlreport
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class MySqlController extends ActionController
{
    /**
     * @var \StefanFroemken\Mysqlreport\Domain\Repository\StatusRepository
     */
    protected $statusRepository;

    /**
     * @var \StefanFroemken\Mysqlreport\Domain\Repository\VariablesRepository
     */
    protected $variablesRepository;

    /**
     * inject statusRepository
     *
     * @param StatusRepository $statusRepository
     * @return void
     */
    public function injectStatusRepository(StatusRepository $statusRepository)
    {
        $this->statusRepository = $statusRepository;
    }

    /**
     * inject variablesRepository
     *
     * @param VariablesRepository $variablesRepository
     * @return void
     */
    public function injectVariablesRepository(VariablesRepository $variablesRepository)
    {
        $this->variablesRepository = $variablesRepository;
    }

    /**
     * introduction page
     *
     * @return void
     */
    public function indexAction()
    {
        $this->view->assign('status', $this->statusRepository->findAll());
        $this->view->assign('variables', $this->variablesRepository->findAll());
    }

    /**
     * query cache action
     *
     * @return void
     */
    public function queryCacheAction()
    {
        $this->view->assign('status', $this->statusRepository->findAll());
        $this->view->assign('variables', $this->variablesRepository->findAll());
    }

    /**
     * innoDb Buffer action
     *
     * @return void
     */
    public function innoDbBufferAction()
    {
        $this->view->assign('status', $this->statusRepository->findAll());
        $this->view->assign('variables', $this->variablesRepository->findAll());
    }

    /**
     * thread cache action
     *
     * @return void
     */
    public function threadCacheAction()
    {
        $this->view->assign('status', $this->statusRepository->findAll());
        $this->view->assign('variables', $this->variablesRepository->findAll());
    }

    /**
     * table cache action
     *
     * @return void
     */
    public function tableCacheAction()
    {
        $this->view->assign('status', $this->statusRepository->findAll());
        $this->view->assign('variables', $this->variablesRepository->findAll());
    }
}
