<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Analysis;

/**
 * Instances Check Analysis for InnoDB
 */
class InstancesCheckAnalysis extends AbstractAnalysis
{
    public function getIdentifier(): string
    {
        return 'InstancesCheck';
    }

    public function getTitle(): string
    {
        return 'Instances Check';
    }

    public function getGroup(): string
    {
        return 'InnoDB';
    }

    public function getDescription(): string
    {
        return 'Each Instance should be allocated to 1GB.';
    }

    public function getRecommendation(): string
    {
        return sprintf(
            'You just have defined %d Instances. This would enough to allocate %d GB. If your innodb_buffer_pool_size is less than 1GB, 1 Instance is OK.',
            $this->getPlainResult(),
            $this->getPlainResult()
        );
    }

    public function getFormula(): string
    {
        return 'innodb_buffer_pool_instances = 1 or innodb_buffer_pool_size (in GB) = innodb_buffer_pool_instances';
    }

    public function getCssClass(): string
    {
        $poolInstances = $this->getPlainResult();
        $poolSize = $this->getCleanedVariables()['innodb_buffer_pool_size'];

        $innodbBufferShouldBe = $poolInstances * (1 * 1024 * 1024 * 1024);
        if ($poolSize < (1 * 1024 * 1024 * 1024) && $poolInstances === 1) {
            $cssClass = 'success';
        } elseif ($innodbBufferShouldBe !== $poolSize) {
            $cssClass = 'danger';
        } else {
            $cssClass = 'success';
        }

        return $cssClass;
    }

    public function getPlainResult()
    {
        return (int)$this->getCleanedVariables()['innodb_buffer_pool_instances'];
    }
}
