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
     * @var ProfileRepository
     */
    protected $databaseRepository;

    public function __construct(ProfileRepository $databaseRepository)
    {
        $this->databaseRepository = $databaseRepository;
    }

    public function filesortAction(): void
    {
        $this->view->assign('queries', $this->databaseRepository->findQueriesWithFilesort());
    }

    public function fullTableScanAction(): void
    {
        $this->view->assign('queries', $this->databaseRepository->findQueriesWithFullTableScan());
    }
}
