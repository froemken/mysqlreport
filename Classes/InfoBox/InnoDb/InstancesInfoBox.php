<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\InnoDb;

use StefanFroemken\Mysqlreport\Domain\Model\StatusValues;
use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxInterface;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxStateInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;

/**
 * InfoBox to inform about InnoDB buffer instances
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.innodb',
)]
final readonly class InstancesInfoBox implements InfoBoxInterface, InfoBoxStateInterface
{
    public function getTitle(): string
    {
        return 'Instances';
    }

    public function getBody(StatusValues $statusValues, Variables $variables): string
    {
        if (!isset($statusValues['Innodb_page_size']) || !isset($variables['innodb_buffer_pool_instances'])) {
            return '';
        }

        $instances = $this->getInstances($variables);

        $content = [
            'Each Instance should be allocated to 1GB.',
            'You just have defined %d Instances.',
            'This would enough to allocate %d GB',
            'If your innodb_buffer_pool_size is less than 1GB, 1 Instance is OK.',
        ];

        return sprintf(
            implode(' ', $content),
            $instances,
            $instances,
        );
    }

    private function getInstances(Variables $variables): int
    {
        return (int)$variables['innodb_buffer_pool_instances'];
    }

    public function getState(StatusValues $statusValues, Variables $variables): ContextualFeedbackSeverity
    {
        $innodbBufferShouldBe = $variables['innodb_buffer_pool_instances'] * (1024 * 1024 * 1024); // Instances * 1 GB
        if (
            $variables['innodb_buffer_pool_size'] < (1024 * 1024 * 1024)
            && $variables['innodb_buffer_pool_instances'] === 1
        ) {
            return ContextualFeedbackSeverity::OK;
        }

        if ($innodbBufferShouldBe !== $variables['innodb_buffer_pool_size']) {
            return ContextualFeedbackSeverity::ERROR;
        }

        return ContextualFeedbackSeverity::OK;
    }
}
