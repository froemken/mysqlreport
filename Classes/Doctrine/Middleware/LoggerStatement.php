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
     * @var array<int, ParameterType>
     */
    private array $types = [];

    public function __construct(
        readonly private StatementInterface $wrappedStatement,
        readonly private MySqlReportSqlLogger $logger,
        readonly private string $sql,
        readonly private \SplQueue $queries,
    ) {}

    public function bindValue(int|string $param, mixed $value, ParameterType $type = ParameterType::STRING): void
    {
        $this->params[$param] = $value;
        $this->types[$param]  = $type;

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
}
