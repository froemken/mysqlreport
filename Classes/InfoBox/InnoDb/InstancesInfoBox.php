<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\InnoDb;

use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use StefanFroemken\Mysqlreport\Enumeration\StateEnumeration;
use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\Menu\Page;

/**
 * InfoBox to inform about InnoDB buffer instances
 */
class InstancesInfoBox extends AbstractInfoBox
{
    protected string $pageIdentifier = 'innoDb';

    protected string $title = 'Instances';

    public function renderBody(Page $page): string
    {
        if (!isset($page->getStatusValues()['Innodb_page_size'])) {
            $this->shouldBeRendered = false;
            return '';
        }

        $instances = $this->getInstances($page->getVariables());

        $content = [];
        $content[] = 'Each Instance should be allocated to 1GB.';
        $content[] = 'You just have defined %d Instances.';
        $content[] = 'This would enough to allocate %d GB';
        $content[] = 'If your innodb_buffer_pool_size is less than 1GB, 1 Instance is OK.';

        return sprintf(
            implode(' ', $content),
            $instances,
            $instances
        );
    }

    protected function getInstances(Variables $variables): int
    {
        $innodbBufferShouldBe = $variables['innodb_buffer_pool_instances'] * (1 * 1024 * 1024 * 1024); // Instances * 1 GB
        if ($variables['innodb_buffer_pool_size'] < (1 * 1024 * 1024 * 1024) && $variables['innodb_buffer_pool_instances'] === 1) {
            $this->setState(StateEnumeration::STATE_OK);
        } elseif ($innodbBufferShouldBe !== $variables['innodb_buffer_pool_size']) {
            $this->setState(StateEnumeration::STATE_ERROR);
        } else {
            $this->setState(StateEnumeration::STATE_OK);
        }

        return (int)$variables['innodb_buffer_pool_instances'];
    }
}
