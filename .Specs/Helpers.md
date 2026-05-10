# Helpers

This document specifies the various "Helper" classes within the extension. These classes follow a modern development pattern, acting as small, stateless, and focused services (tools) that encapsulate a specific piece of logic. They are preferred over static `Utility` classes as they are easily testable and support dependency injection.

---

## `DownloadHelper`

-   **Location**: `Classes/Helper/DownloadHelper.php`
-   **Purpose**: Provides methods to generate downloadable files (CSV or JSON) from a given set of records.
-   **Dependencies**: `Psr\Http\Message\ResponseFactoryInterface`, `Psr\Log\LoggerInterface`.
-   **Key Methods**:
    -   `asCSV(array $headerRow, array $records)`: Creates a CSV file response. It uses TYPO3's `CsvUtility` for correct formatting.
    -   `asJSON(array $records)`: Creates a JSON file response. It includes a fallback mechanism that Base64-encodes query strings if a UTF-8 encoding error occurs during JSON conversion.
-   **Usage**: Primarily used by the `ProfileController` for the download functionality.

---

## `ExplainQueryHelper`

-   **Location**: `Classes/Helper/ExplainQueryHelper.php`
-   **Purpose**: Enriches a `QueryInformation` object with the result of an `EXPLAIN` query.
-   **Dependencies**: `StefanFroemken\Mysqlreport\Configuration\ExtConf`, `TYPO3\CMS\Core\Database\ConnectionPool`, `Psr\Log\LoggerInterface`.
-   **Key Method**:
    -   `updateQueryInformation(QueryInformation $queryInformation)`: Checks if `activateExplainQuery` is enabled in the extension configuration. If so, and if the query is a `SELECT` statement, it executes `EXPLAIN` on the query and adds the result to the `QueryInformation` object.
-   **Usage**: Called from `LoggerWithQueryTimeConnection::__destruct()` for every logged query before it is persisted.

---

## `QueryCacheHelper`

-   **Location**: `Classes/Helper/QueryCacheHelper.php`
-   **Purpose**: Provides a reusable check to determine if the (legacy) MySQL Query Cache is enabled.
-   **Dependencies**: None.
-   **Key Method**:
    -   `isQueryCacheEnabled(Variables $variables)`: Takes a `Variables` model object and checks the `query_cache_type` variable. It correctly handles both string ('ON') and integer (1) representations for the enabled state.
-   **Usage**: Used by InfoBoxes related to the Query Cache to decide whether to render their content.

---

## `QueryParamsHelper`

-   **Location**: `Classes/Helper/QueryParamsHelper.php`
-   **Purpose**: Reconstructs a full, readable SQL query string from a prepared statement's SQL and its parameters.
-   **Dependencies**: `TYPO3\CMS\Core\Database\ConnectionPool`, `Psr\Log\LoggerInterface`.
-   **Key Method**:
    -   `getQueryWithReplacedParams(string $sql, array $params, array $types)`: Safely replaces `?` placeholders in an SQL string with their corresponding values, using Doctrine's type conversion system to ensure correct quoting and formatting.
-   **Usage**: Called by `MySqlReportSqlLogger` to create the final, human-readable query string that gets stored in the database.
