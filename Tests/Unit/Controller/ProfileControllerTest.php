<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Tests\Unit\Controller;

use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use StefanFroemken\Mysqlreport\Controller\ProfileController;
use StefanFroemken\Mysqlreport\Domain\Repository\QueryInformationRepository;
use StefanFroemken\Mysqlreport\Helper\DownloadHelper;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ProfileControllerTest extends UnitTestCase
{
    #[Test]
    public function downloadActionDispatchesCsvDownload(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn([
            'action' => 'download',
            'uniqueIdentifier' => 'test-uuid',
            'downloadType' => 'csv',
        ]);
        $response = $this->createMock(ResponseInterface::class);

        $repository = $this->createMock(QueryInformationRepository::class);
        $repository->expects(self::once())
            ->method('getQueryInformationRecordsForDownloadByUniqueIdentifier')
            ->with('test-uuid', self::isType('array'))
            ->willReturn([['query' => 'SELECT 1']]);

        $downloadHelper = $this->createMock(DownloadHelper::class);
        $downloadHelper->expects(self::once())
            ->method('asCSV')
            ->willReturn($response);

        /** @var ModuleTemplateFactory $moduleTemplateFactory */
        $moduleTemplateFactory = (new \ReflectionClass(ModuleTemplateFactory::class))
            ->newInstanceWithoutConstructor();

        $controller = new ProfileController(
            $repository,
            $moduleTemplateFactory,
            $downloadHelper,
        );

        $actualResponse = $controller->handleRequest($request);
        self::assertSame($response, $actualResponse);
    }

    #[Test]
    public function downloadActionDispatchesJsonDownload(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn([
            'action' => 'download',
            'uniqueIdentifier' => 'test-uuid',
            'downloadType' => 'json',
        ]);
        $response = $this->createMock(ResponseInterface::class);

        $repository = $this->createMock(QueryInformationRepository::class);
        $repository->expects(self::once())
            ->method('getQueryInformationRecordsForDownloadByUniqueIdentifier')
            ->with('test-uuid', self::isType('array'))
            ->willReturn([['query' => 'SELECT 1']]);

        $downloadHelper = $this->createMock(DownloadHelper::class);
        $downloadHelper->expects(self::once())
            ->method('asJSON')
            ->willReturn($response);

        /** @var ModuleTemplateFactory $moduleTemplateFactory */
        $moduleTemplateFactory = (new \ReflectionClass(ModuleTemplateFactory::class))
            ->newInstanceWithoutConstructor();

        $controller = new ProfileController(
            $repository,
            $moduleTemplateFactory,
            $downloadHelper,
        );

        $actualResponse = $controller->handleRequest($request);
        self::assertSame($response, $actualResponse);
    }

    #[Test]
    public function downloadActionThrowsExceptionForEmptyDownloadType(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(1673904554);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn([
            'action' => 'download',
            'uniqueIdentifier' => 'test-uuid',
            'downloadType' => '',
        ]);

        $repository = $this->createMock(QueryInformationRepository::class);
        /** @var ModuleTemplateFactory $moduleTemplateFactory */
        $moduleTemplateFactory = (new \ReflectionClass(ModuleTemplateFactory::class))
            ->newInstanceWithoutConstructor();
        $downloadHelper = $this->createMock(DownloadHelper::class);

        $controller = new ProfileController(
            $repository,
            $moduleTemplateFactory,
            $downloadHelper,
        );

        $controller->handleRequest($request);
    }

    #[Test]
    public function downloadActionThrowsExceptionForInvalidDownloadType(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(1673904777);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn([
            'action' => 'download',
            'uniqueIdentifier' => 'test-uuid',
            'downloadType' => 'xml',
        ]);

        $repository = $this->createMock(QueryInformationRepository::class);
        /** @var ModuleTemplateFactory $moduleTemplateFactory */
        $moduleTemplateFactory = (new \ReflectionClass(ModuleTemplateFactory::class))
            ->newInstanceWithoutConstructor();
        $downloadHelper = $this->createMock(DownloadHelper::class);

        $controller = new ProfileController(
            $repository,
            $moduleTemplateFactory,
            $downloadHelper,
        );

        $controller->handleRequest($request);
    }
}
