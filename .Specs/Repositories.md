# Repositories

This document specifies the data layer of the `mysqlreport` extension. It covers repositories that fetch data directly from the database. This reflects the current implementation.

## General Guidelines

- Repositories are not based on Extbase's `\TYPO3\CMS\Extbase\Persistence\Repository`. They are custom data mappers.
- Database access is performed via a custom `DatabaseConnectionTrait` which uses the `ConnectionPool` to execute raw SQL queries.
- Repositories are typically `readonly` classes.

---

## `StatusRepository`

The `StatusRepository` is responsible for fetching the global status variables from the MySQL/MariaDB database.

### Functionality

- The `findAll()` method executes the query `SHOW GLOBAL STATUS`.
- It fetches all resulting rows and compiles them into a single key-value array.
- It creates a **single instance** of the `StefanFroemken\Mysqlreport\Domain\Model\StatusValues` model, passing the complete key-value array to its constructor.
- It returns this single `StatusValues` object.

### Class Location

`Classes/Domain/Repository/StatusRepository.php`

---

## `VariablesRepository`

The `VariablesRepository` is responsible for fetching the global configuration variables from the MySQL/MariaDB database.

### Functionality

- The `findAll()` method executes the query `SHOW GLOBAL VARIABLES`.
- It fetches all resulting rows and compiles them into a single key-value array.
- It creates a **single instance** of the `StefanFroemken\Mysqlreport\Domain\Model\Variables` model, passing the complete key-value array to its constructor.
- It returns this single `Variables` object.

### Class Location

`Classes/Domain/Repository/VariablesRepository.php`

---

## `QueryInformationRepository`

The `QueryInformationRepository` is the primary interface for reading and writing query log data from the `tx_mysqlreport_query_information` table. It does not map results to domain models, but returns associative arrays.

### Functionality

- **Data Source:** `tx_mysqlreport_query_information` table.
- **Write Operations:**
    - `bulkInsert(array $queries)`: Efficiently inserts a large number of query records in chunks.
- **Read Operations:**
    - Provides various `find*` and `get*` methods to retrieve aggregated or filtered query data (e.g., `findQueryInformationRecordsForCall`, `getQueryInformationRecordsByUniqueIdentifier`).
    - Most read methods return an `array` of associative arrays, not objects.
    - `getQueryInformationRecordByUid(int $uid)` returns a single associative array.
- **Special Methods:**
    - `getQueryProfiling(array $queryInformationRecord)`: Executes a given SELECT query with `SET profiling=1;` and returns the profiling results.
- **Event Dispatching:**
    - Nearly all read methods dispatch a `ModifyQueryInformationRecordsEvent` before returning the data. This allows other parts of the system to modify the result set dynamically.

### Class Location

`Classes/Domain/Repository/QueryInformationRepository.php`
