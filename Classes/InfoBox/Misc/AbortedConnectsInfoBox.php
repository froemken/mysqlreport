<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\Misc;

use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\Traits\GetStatusValuesAndVariablesTrait;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * InfoBox to inform about aborted connects
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.misc',
    attributes: ['priority' => 50],
)]
class AbortedConnectsInfoBox extends AbstractInfoBox
{
    use GetStatusValuesAndVariablesTrait;

    protected const TITLE = 'Aborted Connects';

    public function renderBody(): string
    {
        if (!isset($this->getStatusValues()['Aborted_connects'])) {
            return '';
        }

        $content = [];
        $content[] = 'You have %d aborted connects.';
        $content[] = 'If this value is high it could be that you have many wrong logins.';
        $content[] = 'Please check your application for wrong authentication data.';

        return sprintf(
            implode(' ', $content),
            $this->getStatusValues()['Aborted_connects'],
        );
    }
}
