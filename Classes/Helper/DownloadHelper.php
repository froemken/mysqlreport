<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Helper;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\CsvUtility;

/**
 * Helper to download records as CSV or JSON
 */
class DownloadHelper
{
    private string $csvDelimiter = ';';

    private string $csvQuote = '"';

    public function asCSV(array $headerRow, array $records): void
    {
        // Create result
        $result[] = CsvUtility::csvValues($headerRow, $this->csvDelimiter, $this->csvQuote);
        foreach ($records as $record) {
            $result[] = CsvUtility::csvValues($record, $this->csvDelimiter, $this->csvQuote);
        }

        $this->generateDownloadResponse(implode(CRLF, $result), 'csv');
    }

    public function asJSON(array $records): void
    {
        try {
            $json = json_encode($records, JSON_THROW_ON_ERROR) ?: '';
        } catch (\JsonException $e) {
            $json = '';
        }

        $this->generateDownloadResponse($json, 'json');
    }

    private function generateDownloadResponse(string $result, string $fileExt): void
    {
        // Creating output header:
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $this->getFilename($fileExt));
        // Cache-Control header is needed here to solve an issue with browser IE and
        // versions lower than 9. See for more information: http://support.microsoft.com/kb/323308
        header("Cache-Control: ''");
        // Printing the content of the CSV lines:
        echo $result;
        // Exits:
        die;
    }

    private function getFilename(string $fileExt): string
    {
        $fileBody = 'mysqlreport_' . date('dmy-Hi');
        if ($fileExt === '') {
            return $fileBody . 'wrong_file_ext.log';
        }

        return $fileBody . '.' . $fileExt;
    }
}
