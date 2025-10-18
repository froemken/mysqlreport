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
 * InfoBox to inform about InnoDB buffer log file size
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.innodb',
)]
class LogFileSizeInfoBox extends AbstractInfoBox implements InfoBoxStateInterface
{
    use GetStatusValuesAndVariablesTrait;

    protected const TITLE = 'Log File Size';

    public function renderBody(): string
    {
        if (
            !isset(
                $this->getStatusValues()['Innodb_page_size'],
                $this->getVariables()['innodb_log_files_in_group'],
            )
            || (int)$this->getVariables()['innodb_log_files_in_group'] === 0
        ) {
            return '';
        }

        $logFileSize = $this->getLogFileSize();

        $content = [];
        $content[] = 'Your current log file size is %d Bytes.';
        $content[] = 'A good value for your log files would be a little bit higher than: %d Bytes.';
        $content[] = 'Please call this test only on high peak of your server.';
        $content[] = 'Be careful: When changing this value you have to cleanly shutdown your MySQL,';
        $content[] = 'backup and remove the ib_logfile*-files from data-directory.';
        $content[] = 'Start your server. If there are no errors you can safely delete your backed up ib_logfile*-files.';

        return sprintf(
            implode(' ', $content),
            $logFileSize['value'],
            $logFileSize['niceToHave'],
        );
    }

    /**
     * find a good size for log files
     *
     * @link http://www.psce.com/blog/2012/04/10/what-is-the-proper-size-of-innodb-logs/
     *
     * @return array<string, int>
     */
    protected function getLogFileSize(): array
    {
        $variables = $this->getVariables();

        return [
            'value' => $variables['innodb_log_file_size'],
            'niceToHave' => $this->getSizeOfEachLogFile(),
        ];
    }

    private function getSizeOfEachLogFile(): int
    {
        $variables = $this->getVariables();
        $status = $this->getStatusValues();

        $bytesWrittenEachSecond = $status['Innodb_os_log_written'] / $status['Uptime'];
        $bytesWrittenEachHour = $bytesWrittenEachSecond * 60 * 60;

        return (int)($bytesWrittenEachHour / $variables['innodb_log_files_in_group']);
    }

    public function getState(): StateEnumeration
    {
        $variables = $this->getVariables();
        $sizeOfEachLogFile = $this->getSizeOfEachLogFile();

        if ($sizeOfEachLogFile < 5242880 || $sizeOfEachLogFile < $variables['innodb_log_file_size']) {
            $state = StateEnumeration::STATE_OK;
        } else {
            $state = StateEnumeration::STATE_ERROR;
        }

        return $state;
    }
}
