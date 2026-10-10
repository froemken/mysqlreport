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
use Psr\Http\Message\ServerRequestInterface;
use StefanFroemken\Mysqlreport\Domain\Repository\QueryInformationRepository;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;

#[AsController]
final readonly class FullTableScanController
{
    public function __construct(
        private QueryInformationRepository $queryInformationRepository,
        private ModuleTemplateFactory $moduleTemplateFactory,
    ) {}

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $moduleTemplate->getDocHeaderComponent()->setShortcutContext(
            'mysqlreport_fulltablescan',
            'MySQL Report - Full Table Scan',
        );

        $moduleTemplate->assign(
            'queryInformationRecords',
            $this->queryInformationRepository->findQueryInformationRecordsWithFullTableScan(),
        );

        return $moduleTemplate->renderResponse('FullTableScan/Index');
    }
}
