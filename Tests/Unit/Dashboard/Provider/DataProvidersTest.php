<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Tests\Unit\Dashboard\Provider;

use PHPUnit\Framework\Attributes\Test;
use StefanFroemken\Mysqlreport\Dashboard\Provider\CreatedTempTablesDataProvider;
use StefanFroemken\Mysqlreport\Dashboard\Provider\HandlerReadNextDataProvider;
use StefanFroemken\Mysqlreport\Dashboard\Provider\InnoDbBufferDataProvider;
use StefanFroemken\Mysqlreport\Dashboard\Provider\MaxUsedConnectionsDataProvider;
use StefanFroemken\Mysqlreport\Dashboard\Provider\QueryTypesDataProvider;
use StefanFroemken\Mysqlreport\Domain\Model\StatusValues;
use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use StefanFroemken\Mysqlreport\Domain\Repository\StatusRepositoryInterface;
use StefanFroemken\Mysqlreport\Domain\Repository\VariablesRepositoryInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class DataProvidersTest extends UnitTestCase
{
    #[Test]
    public function createdTempTablesDataProviderBuildsChartDataLazily(): void
    {
        $statusRepository = $this->createMock(StatusRepositoryInterface::class);
        $statusValues = new StatusValues([
            'Created_tmp_disk_tables' => 10,
            'Created_tmp_tables' => 25,
        ]);
        $statusRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($statusValues);

        $provider = new CreatedTempTablesDataProvider($statusRepository);
        $data = $provider->getChartData();

        self::assertSame([10, 15], $data['datasets'][0]['data']);
    }

    #[Test]
    public function handlerReadNextDataProviderBuildsChartDataLazily(): void
    {
        $statusRepository = $this->createMock(StatusRepositoryInterface::class);
        $statusValues = new StatusValues([
            'Handler_read_rnd_next' => 50,
            'Handler_read_next' => 100,
        ]);
        $statusRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($statusValues);

        $provider = new HandlerReadNextDataProvider($statusRepository);
        $data = $provider->getChartData();

        self::assertSame([50, 100], $data['datasets'][0]['data']);
    }

    #[Test]
    public function innoDbBufferDataProviderBuildsChartDataLazily(): void
    {
        $statusRepository = $this->createMock(StatusRepositoryInterface::class);
        $variablesRepository = $this->createMock(VariablesRepositoryInterface::class);

        $statusValues = new StatusValues([
            'Innodb_buffer_pool_pages_data' => 100,
            'Innodb_buffer_pool_pages_misc' => 20,
            'Innodb_buffer_pool_pages_free' => 30,
        ]);
        $variables = new Variables([
            'innodb_page_size' => 16384,
        ]);

        $statusRepository->expects($this->once())->method('findAll')->willReturn($statusValues);
        $variablesRepository->expects($this->once())->method('findAll')->willReturn($variables);

        $provider = new InnoDbBufferDataProvider($statusRepository, $variablesRepository);
        $data = $provider->getChartData();

        self::assertCount(3, $data['datasets'][0]['data']);
    }

    #[Test]
    public function maxUsedConnectionsDataProviderBuildsChartDataLazily(): void
    {
        $statusRepository = $this->createMock(StatusRepositoryInterface::class);
        $variablesRepository = $this->createMock(VariablesRepositoryInterface::class);

        $statusValues = new StatusValues([
            'Max_used_connections' => 40,
        ]);
        $variables = new Variables([
            'max_connections' => 100,
        ]);

        $statusRepository->expects($this->once())->method('findAll')->willReturn($statusValues);
        $variablesRepository->expects($this->once())->method('findAll')->willReturn($variables);

        $provider = new MaxUsedConnectionsDataProvider($statusRepository, $variablesRepository);
        $data = $provider->getChartData();

        self::assertSame([40, 60], $data['datasets'][0]['data']);
    }

    #[Test]
    public function queryTypesDataProviderBuildsChartDataLazily(): void
    {
        $statusRepository = $this->createMock(StatusRepositoryInterface::class);
        $statusValues = new StatusValues([
            'Com_select' => 50,
            'Com_delete' => 5,
            'Com_insert' => 20,
            'Com_update' => 25,
            'Com_replace' => 0,
        ]);
        $statusRepository->expects($this->once())->method('findAll')->willReturn($statusValues);

        $provider = new QueryTypesDataProvider($statusRepository);
        $data = $provider->getChartData();

        self::assertSame([50, 20, 25, 5, 0], $data['datasets'][0]['data']);
    }
}
