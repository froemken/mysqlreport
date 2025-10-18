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
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Utility\CsvUtility;

/**
 * Helper to download records as CSV or JSON
 */
readonly class DownloadHelper
{
    private const CSV_DELIMITER = ';';

    private const CSV_QUOTE = '"';

    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private LoggerInterface $logger,
    ) {}

    /**
     * @param array<int, string> $headerRow
     * @param array<string, mixed> $records
     */
    public function asCSV(array $headerRow, array $records): ResponseInterface
    {
        // Create the result
        $result[] = CsvUtility::csvValues($headerRow, self::CSV_DELIMITER, self::CSV_QUOTE);
        foreach ($records as $record) {
            $result[] = CsvUtility::csvValues($record, self::CSV_DELIMITER, self::CSV_QUOTE);
        }

        return $this->generateDownloadResponse(implode(CRLF, $result), 'csv');
    }

    /**
     * @param array<string, mixed> $records
     */
    public function asJSON(array $records): ResponseInterface
    {
        try {
            $json = json_encode($records, JSON_THROW_ON_ERROR) ?: '';
        } catch (\JsonException $exception) {
            $this->logger->error('Error while encoding JSON in DownloadHelper', [
                'exception' => $exception,
            ]);
            $json = '';
        }

        return $this->generateDownloadResponse($json, 'json');
    }

    private function generateDownloadResponse(string $result, string $fileExt): ResponseInterface
    {
        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'application/octet-stream')
            ->withHeader('Content-Disposition', 'attachment; filename=' . $this->getFilename($fileExt));

        $response->getBody()->write($result);

        return $response;
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
