<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Analysis\InnoDbBuffer;

use StefanFroemken\Mysqlreport\Analysis\AbstractAnalysis;

/**
 * LogFileSize Analysis for InnoDB
 */
class LogFileSizeAnalysis extends AbstractAnalysis
{
    public function getIdentifier(): string
    {
        return 'LogFileSize';
    }

    public function getTitle(): string
    {
        return 'Log File Size';
    }

    public function getGroup(): string
    {
        return 'InnoDB';
    }

    public function getDescription(): string
    {
        return 'Please call this test only on high peak of your server. Be careful: When changing this value you have to cleanly shutdown your MySQL, backup and remove the ib_logfile*-files from data-directory. Start your server. If there are no errors you can safely delete your backed up ib_logfile*-files.';
    }

    public function getRecommendation(): string
    {
        return sprintf(
            'Your current log file size is %d Bytes. A good value for your log files would be a little bit higher than: %d Bytes.',
            $this->getCleanedVariables()['innodb_log_file_size'],
            (int)$this->getPlainResult()
        );
    }

    public function getFormula(): string
    {
        return '(((innodb_os_log_written / uptime) * 60 * 60) / innodb_log_files_in_group) < innodb_log_file_size';
    }

    public function getCssClass(): string
    {
        $sizeOfEachLogFile = (int)$this->getPlainResult();

        if ($sizeOfEachLogFile < 5242880 || $sizeOfEachLogFile < $this->getVariables()['innodb_log_file_size']) {
            $cssClass = 'success';
        } else {
            $cssClass = 'danger';
        }

        return $cssClass;
    }

    public function getPlainResult()
    {
        $bytesWrittenEachSecond = $this->getCleanedStatus()['innodb_os_log_written'] / $this->getCleanedStatus()['uptime'];
        $bytesWrittenEachHour = $bytesWrittenEachSecond * 60 * 60;
        return ($bytesWrittenEachHour / $this->getVariables()['innodb_log_files_in_group']);
    }
}
