# Doctrine Middleware for Query Logging

This document specifies the entire process of how database queries are logged within the `mysqlreport` extension. This is achieved by injecting a custom middleware into the Doctrine DBAL layer of TYPO3.

## Process Flow Overview

The logging is not a simple hook but a chain of nested wrapper classes that augment the default Doctrine behavior.

1.  **Registration**: A driver middleware is registered in `ext_localconf.php`.
2.  **Configuration**: The `ExtConf` class reads settings from `ext_conf_template.txt` to determine if and how logging should occur.
3.  **Activation Check**: The middleware class checks if logging is enabled for the current context (FE/BE) and if the database connection is compatible (MySQL/MariaDB).
4.  **Wrapping Chain**: If active, a chain of custom classes is instantiated, each wrapping a component of the default Doctrine driver.
5.  **Data Collection**: The `LoggerWithQueryTimeConnection` class acts as the central collector, gathering all executed queries during a single PHP request.
6.  **Data Persistence**: In the `__destruct()` method, all collected queries are processed (e.g., adding `EXPLAIN` data) and then written to the database.

## Configuration (`ExtConf.php`)

The `StefanFroemken\Mysqlreport\Configuration\ExtConf` class provides type-safe access to the extension's settings. The following settings directly control the behavior of the Doctrine middleware:

-   **`enableFrontendLogging` (bool)**: If `true`, the middleware will be active for all frontend requests.
-   **`enableBackendLogging` (bool)**: If `true`, the middleware will be active for all backend requests.
-   **`activateExplainQuery` (bool)**: If `true`, an additional `EXPLAIN` query is executed for every logged `SELECT` statement. The result is stored in the `explain_query` field.
    -   **Important**: This provides deep insights but can have a minor performance impact on the TYPO3 instance, as it effectively doubles the number of `SELECT` queries. It should be used consciously for debugging and analysis.

The `slowQueryThreshold` setting is **not** used by the middleware; it is only used for displaying data in the backend module.

## System Report Integration (`StatusReport.php`)

To alert administrators about the performance implications of the `activateExplainQuery` setting, the extension integrates with TYPO3's "Reports" module.

-   **Class**: `StefanFroemken\Mysqlreport\Report\StatusReport`
-   **Interface**: Implements `StatusProviderInterface`.
-   **Registration**: The class is explicitly tagged with `reports.status` in `Configuration/Services.yaml`. (Note: This is redundant, as TYPO3 automatically tags all classes implementing this interface).
-   **Functionality**: It checks the value of `ExtConf::isActivateExplainQuery()`.
    -   If **active**, it displays a **WARNING** in the system status report, reminding the admin that this setting can slow down the system.
    -   If **inactive**, it displays an **OK** status.
-   **Purpose**: This serves as a crucial reminder for administrators to deactivate the `EXPLAIN` feature on production systems after debugging is complete.

## Component Analysis

### 1. Registration (`ext_localconf.php`)

- A Doctrine driver middleware is registered via `$GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['driverMiddlewares']`.
- **Target Class**: `StefanFroemken\Mysqlreport\Doctrine\Middleware\LoggerWithQueryTimeMiddleware`

### 2. Middleware (`LoggerWithQueryTimeMiddleware`)

- Implements `UsableForConnectionInterface`.
- The `canBeUsedForConnection()` method checks:
    1.  Is it the default connection?
    2.  Is the driver MySQL/MariaDB compatible (via `isValidConnectionDriver`)?
    3.  Is logging activated for the current scope (FE/BE) via `ExtConf::isQueryLoggingActivated()`?
- If all checks pass, the `wrap()` method returns a new `LoggerWithQueryTimeDriver`, injecting the original driver.

### 3. Driver (`LoggerWithQueryTimeDriver`)

- A simple wrapper that extends `AbstractDriverMiddleware`.
- Its only purpose is to override the `connect()` method to return a new `LoggerWithQueryTimeConnection`, injecting the original connection.

### 4. Connection (`LoggerWithQueryTimeConnection`)

- The core of the data collection.
- **`__construct()`**: Initializes an `\SplQueue` to hold `QueryInformation` objects and instantiates helper classes.
- **`query()` / `exec()`**: For direct queries, it measures execution time and pushes the resulting `QueryInformation` object to the queue.
- **`prepare()`**: For prepared statements, it returns a `LoggerStatement` wrapper, delegating the logging logic.
- **`__destruct()`**: At the end of the request, it iterates the queue, enriches the data (e.g., with `EXPLAIN`), and calls `QueryInformationRepository->bulkInsert()`.

### 5. Statement (`LoggerStatement`)

- A wrapper for prepared statements.
- **`bindValue()`**: Intercepts and stores all bound parameters and their types.
- **`execute()`**: Measures execution time and calls `MySqlReportSqlLogger->stopQuery()`, passing the SQL, duration, and the collected parameters and types. The result is pushed to the queue.

### 6. Logger Service (`MySqlReportSqlLogger`)

- Acts as a bridge between the raw data and the domain model.
- **`stopQuery()`**:
    1.  Checks if the query should be logged using `isValidQuery()`. This is a critical step.
    2.  Uses `QueryInformationFactory` to create a pre-filled `QueryInformation` object.
    3.  Uses `QueryParamsHelper` to reconstruct the full SQL string from the prepared statement and its parameters.
    4.  Sets the duration and the final query string on the model object.
- **`isValidQuery()`**: This is a crucial guard method with two main purposes:
    1.  **Preventing Noise**: It skips logging for internal analysis queries like `EXPLAIN ...` or queries on `information_schema`.
    2.  **Preventing Infinite Loops**: It **must** exclude any query that writes to the `tx_mysqlreport_query_information` table. If these write operations were logged, they would trigger another write, ad infinitum.

### 7. Factory (`QueryInformationFactory`)

- A `readonly` class that optimizes data retrieval.
- The constructor is executed only once per request and gathers all request-specific data (IP, Page ID, Request URL, a `uniqueCallIdentifier`, etc.).
- `createNewQueryInformation()` creates a new model object and populates it with this shared data.

### 8. Helper (`QueryParamsHelper`)

- Reconstructs a full, readable SQL query from a prepared statement.
- It safely replaces the `?` placeholders with their corresponding, correctly quoted and escaped values using Doctrine's type conversion system.
