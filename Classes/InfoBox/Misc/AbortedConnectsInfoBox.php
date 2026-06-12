<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\Misc;

use StefanFroemken\Mysqlreport\Domain\Model\StatusValues;
use StefanFroemken\Mysqlreport\Domain\Model\Variables;

use StefanFroemken\Mysqlreport\InfoBox\InfoBoxInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * InfoBox to inform about aborted connects
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.misc',
    attributes: ['priority' => 50],
)]
final readonly class AbortedConnectsInfoBox implements InfoBoxInterface
{
    public const TITLE = 'Aborted Connects';

    public function __construct(
        private StatusValues $statusValues,
        private Variables $variables,
    ) {}

public function getBody(): string
    {
        if (!isset($this->statusValues['Aborted_connects'])) {
            return '';
        }

        $content = [];
        $content[] = 'You have %d aborted connects.';
        $content[] = 'If this value is high it could be that you have many wrong logins.';
        $content[] = 'Please check your application for wrong authentication data.';

        return sprintf(
            implode(' ', $content),
            $this->statusValues['Aborted_connects'],
        );
    }
}
