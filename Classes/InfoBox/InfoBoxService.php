<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox;

use StefanFroemken\Mysqlreport\Domain\Model\StatusValues;
use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use StefanFroemken\Mysqlreport\Domain\Repository\StatusRepositoryInterface;
use StefanFroemken\Mysqlreport\Domain\Repository\VariablesRepositoryInterface;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;

final readonly class InfoBoxService
{
    public function __construct(
        private StatusRepositoryInterface $statusRepository,
        private VariablesRepositoryInterface $variablesRepository,
    ) {}

    /**
     * @param iterable<InfoBoxInterface> $infoBoxes
     * @return InfoBoxItem[]
     */
    public function buildInfoBoxes(iterable $infoBoxes): array
    {
        $statusValues = $this->statusRepository->findAll();
        $variables = $this->variablesRepository->findAll();

        $items = [];
        foreach ($infoBoxes as $infoBox) {
            $item = $this->buildInfoBoxItem($infoBox, $statusValues, $variables);
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
    ): ?InfoBoxItem {
        $body = $infoBox->getBody($statusValues, $variables);
        if ($body === '') {
            return null;
        }

        $state = $infoBox instanceof InfoBoxStateInterface
            ? $infoBox->getState($statusValues, $variables)
            : ContextualFeedbackSeverity::INFO;

        $unorderedList = $infoBox instanceof InfoBoxUnorderedListInterface
            ? $infoBox->getUnorderedList($statusValues, $variables)
            : [];

        return new InfoBoxItem(
            title: $infoBox->getTitle(),
            body: $body,
            state: $state,
            unorderedList: $unorderedList,
        );
    }
}
