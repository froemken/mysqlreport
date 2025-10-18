<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Traits;

use StefanFroemken\Mysqlreport\Domain\Model\StatusValues;
use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use StefanFroemken\Mysqlreport\Domain\Repository\StatusRepository;
use StefanFroemken\Mysqlreport\Domain\Repository\VariablesRepository;

/**
 * Trait to get MySQL/MariaDB status and variables
 */
trait GetStatusValuesAndVariablesTrait
{
    private StatusRepository $statusRepository;

    private VariablesRepository $variablesRepository;

    public function injectStatusRepository(StatusRepository $statusRepository): void
    {
        $this->statusRepository = $statusRepository;
    }

    public function injectVariablesRepository(VariablesRepository $variablesRepository): void
    {
        $this->variablesRepository = $variablesRepository;
    }

    private function getStatusValues(): StatusValues
    {
        return $this->statusRepository->findAll();
    }

    private function getVariables(): Variables
    {
        return $this->variablesRepository->findAll();
    }
}
