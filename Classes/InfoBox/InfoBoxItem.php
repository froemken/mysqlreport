<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox;

use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;

final readonly class InfoBoxItem
{
    /**
     * @param ListElement[] $unorderedList
     */
    public function __construct(
        public string $title,
        public string $body,
        public ContextualFeedbackSeverity $state = ContextualFeedbackSeverity::INFO,
        public array $unorderedList = [],
    ) {}

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getState(): ContextualFeedbackSeverity
    {
        return $this->state;
    }

    /**
     * @return ListElement[]
     */
    public function getUnorderedList(): array
    {
        return $this->unorderedList;
    }
}
