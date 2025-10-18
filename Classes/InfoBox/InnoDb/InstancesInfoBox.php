<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\InnoDb;

use StefanFroemken\Mysqlreport\Enumeration\StateEnumeration;
use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxStateInterface;
use StefanFroemken\Mysqlreport\Traits\GetStatusValuesAndVariablesTrait;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * InfoBox to inform about InnoDB buffer instances
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.innodb',
)]
class InstancesInfoBox extends AbstractInfoBox implements InfoBoxStateInterface
{
    use GetStatusValuesAndVariablesTrait;

    protected const TITLE = 'Instances';

    public function renderBody(): string
    {
        if (!isset($this->getStatusValues()['Innodb_page_size'])) {
            return '';
        }

        if (!isset($this->getVariables()['innodb_buffer_pool_instances'])) {
            return '';
        }

        $instances = $this->getInstances();

        $content = [];
        $content[] = 'Each Instance should be allocated to 1GB.';
        $content[] = 'You just have defined %d Instances.';
        $content[] = 'This would enough to allocate %d GB';
        $content[] = 'If your innodb_buffer_pool_size is less than 1GB, 1 Instance is OK.';

        return sprintf(
            implode(' ', $content),
            $instances,
            $instances,
        );
    }

    protected function getInstances(): int
    {
        $variables = $this->getVariables();

        return (int)$variables['innodb_buffer_pool_instances'];
    }

    public function getState(): StateEnumeration
    {
        $variables = $this->getVariables();

        $innodbBufferShouldBe = $variables['innodb_buffer_pool_instances'] * (1 * 1024 * 1024 * 1024); // Instances * 1 GB
        if (
            $variables['innodb_buffer_pool_size'] < (1 * 1024 * 1024 * 1024)
            && $variables['innodb_buffer_pool_instances'] === 1
        ) {
            $state = StateEnumeration::STATE_OK;
        } elseif ($innodbBufferShouldBe !== $variables['innodb_buffer_pool_size']) {
            $state = StateEnumeration::STATE_ERROR;
        } else {
            $state = StateEnumeration::STATE_OK;
        }

        return $state;
    }
}
