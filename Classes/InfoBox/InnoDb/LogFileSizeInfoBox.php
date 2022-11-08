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
use StefanFroemken\Mysqlreport\Enumeration\StateEnumeration;
use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\Menu\Page;

/**
 * InfoBox to inform about InnoDB buffer log file size
 */
class LogFileSizeInfoBox extends AbstractInfoBox
{
    protected $pageIdentifier = 'innoDb';

    protected $title = 'Log File Size';

    public function renderBody(Page $page): string
    {
        if (!isset($page->getStatusValues()['Innodb_page_size'])) {
            $this->shouldBeRendered = false;
            return '';
        }

        $logFileSize = $this->getLogFileSize($page->getStatusValues(), $page->getVariables());

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
            $logFileSize['niceToHave']
        );
    }

    /**
     * find a good size for log files
     *
     * @link http://www.psce.com/blog/2012/04/10/what-is-the-proper-size-of-innodb-logs/
     */
    protected function getLogFileSize(StatusValues $status, Variables $variables): array
    {
        $bytesWrittenEachSecond = $status['Innodb_os_log_written'] / $status['Uptime'];
        $bytesWrittenEachHour = $bytesWrittenEachSecond * 60 * 60;
        $sizeOfEachLogFile = (int)($bytesWrittenEachHour / $variables['innodb_log_files_in_group']);

        if ($sizeOfEachLogFile < 5242880 || $sizeOfEachLogFile < $variables['innodb_log_file_size']) {
            $this->setState(StateEnumeration::STATE_OK);
        } else {
            $this->setState(StateEnumeration::STATE_ERROR);
        }

        return [
            'value' => $variables['innodb_log_file_size'],
            'niceToHave' => $sizeOfEachLogFile,
        ];
    }
}
