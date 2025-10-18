<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Doctrine\Middleware;

use Doctrine\DBAL\Driver\Result as ResultInterface;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Driver\Statement as StatementInterface;
use Doctrine\DBAL\ParameterType;
use StefanFroemken\Mysqlreport\Domain\Model\QueryInformation;
use StefanFroemken\Mysqlreport\Logger\MySqlReportSqlLogger;

/**
 * Here in the connection, we can wrap our logger around the queries and commands.
 */
class LoggerStatement implements Statement
{
    /**
     * @var array<int, string>
     */
    private array $params = [];

    /**
     * @var array<int, string>
     */
    private array $types = [];

    /**
     * @param \SplQueue<QueryInformation> $queries
     */
    public function __construct(
        private readonly StatementInterface $wrappedStatement,
        private readonly MySqlReportSqlLogger $logger,
        private readonly string $sql,
        private readonly \SplQueue $queries,
    ) {}

    public function bindValue(int|string $param, mixed $value, ParameterType $type = ParameterType::STRING): void
    {
        $this->params[$param] = $value;
        $this->types[$param]  = $this->convertParameterType($type);

        $this->wrappedStatement->bindValue($param, $value, $type);
    }

    public function execute(): ResultInterface
    {
        $startTime = microtime(true);
        $result = $this->wrappedStatement->execute();
        $queryInformation = $this->logger->stopQuery(
            $this->sql,
            microtime(true) - $startTime,
            $this->params,
            $this->types,
        );

        if ($queryInformation instanceof QueryInformation) {
            $this->queries->push($queryInformation);
        }

        return $result;
    }

    /**
     * LARGE_OBJECT is not mapped to a Type object by default.
     * In case of Mysqli it will be mapped to PARAMETER_TYPE_BINARY.
     * As convertParameterType of MysqliStatement is private I have to map such types manually here.
     * As long as this logger works for MySQL and MariaDB only, we should be safe here.
     * Adding further DB types may need some modification here.
     */
    private function convertParameterType(ParameterType $type): string
    {
        return match ($type) {
            ParameterType::NULL,
            ParameterType::STRING,
            ParameterType::ASCII,
            ParameterType::BINARY => 'string',
            ParameterType::INTEGER,
            ParameterType::BOOLEAN => 'integer',
            ParameterType::LARGE_OBJECT => 'binary',
        };
    }
}
