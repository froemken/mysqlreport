<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Controller;

use Psr\Http\Message\ResponseInterface;
use StefanFroemken\Mysqlreport\Domain\Repository\QueryInformationRepository;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class FileSortController extends ActionController
{
    public function __construct(
        private QueryInformationRepository $queryInformationRepository,
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
    ) {}

    public function indexAction(): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $moduleTemplate->getDocHeaderComponent()->setShortcutContext(
            'mysqlreport_filesort',
            'MySQL Report - Filesort',
        );

        $moduleTemplate->assign(
            'queryInformationRecords',
            $this->queryInformationRepository->findQueryInformationRecordsWithFilesort(),
        );

        return $moduleTemplate->renderResponse('FileSort/Index');
    }
}
