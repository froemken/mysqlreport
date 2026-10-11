<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use StefanFroemken\Mysqlreport\Domain\Model\StatusValues;
use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use StefanFroemken\Mysqlreport\Domain\Repository\StatusRepositoryInterface;
use StefanFroemken\Mysqlreport\Domain\Repository\VariablesRepositoryInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;

final readonly class InfoBoxService
{
    public function __construct(
        private StatusRepositoryInterface $statusRepository,
        private VariablesRepositoryInterface $variablesRepository,
        private ConnectionPool $connectionPool,
    ) {}

    /**
     * @param iterable<InfoBoxInterface> $infoBoxes
     * @return InfoBoxItem[]
     */
    public function buildInfoBoxes(iterable $infoBoxes): array
    {
        $statusValues = $this->statusRepository->findAll();
        $variables = $this->variablesRepository->findAll();
        $platform = $this->connectionPool
            ->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME)
            ->getDatabasePlatform();

        $items = [];
        foreach ($infoBoxes as $infoBox) {
            $item = $this->buildInfoBoxItem($infoBox, $statusValues, $variables, $platform);
            if ($item instanceof InfoBoxItem) {
                $items[] = $item;
            }
        }

        return $items;
    }

    private function buildInfoBoxItem(
        InfoBoxInterface $infoBox,
        StatusValues $statusValues,
        Variables $variables,
        AbstractPlatform $platform,
    ): ?InfoBoxItem {
        if ($infoBox instanceof InfoBoxAvailabilityInterface
            && !$infoBox->isAvailable($platform, $variables, $statusValues)
        ) {
            return null;
        }

        $body = $infoBox->getBody($statusValues, $variables);
        if ($body === '') {
            return null;
        }

        return new InfoBoxItem(
            title: $infoBox->getTitle(),
            body: $body,
            state: $this->resolveState($infoBox, $statusValues, $variables),
            unorderedList: $this->resolveUnorderedList($infoBox, $statusValues, $variables),
        );
    }

    private function resolveState(
        InfoBoxInterface $infoBox,
        StatusValues $statusValues,
        Variables $variables,
    ): ContextualFeedbackSeverity {
        if ($infoBox instanceof InfoBoxStateInterface) {
            return $infoBox->getState($statusValues, $variables);
        }

        return ContextualFeedbackSeverity::INFO;
    }

    /**
     * @return ListElement[]
     */
    private function resolveUnorderedList(
        InfoBoxInterface $infoBox,
        StatusValues $statusValues,
        Variables $variables,
    ): array {
        if ($infoBox instanceof InfoBoxUnorderedListInterface) {
            return $infoBox->getUnorderedList($statusValues, $variables);
        }

        return [];
    }
}
