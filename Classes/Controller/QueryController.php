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
 * Controller to show results of FTS and filesort
 */
class QueryController extends AbstractController
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
     * filesort action
     */
    public function filesortAction()
    {
        $this->view->assign('queries', $this->databaseRepository->findQueriesWithFilesort());
    }

    /**
     * using full table scan action
     */
    public function fullTableScanAction()
    {
        $this->view->assign('queries', $this->databaseRepository->findQueriesWithFullTableScan());
    }
}
