# Backend Module

This document specifies the structure and functionality of the `mysqlreport` backend module, including the data flow for each sub-module.

## Overview

The backend module is the primary UI, registered under the `system` category. It provides reports and tools for database analysis.

-   **Main Entry Point**: `Configuration/Backend/Modules.php`
-   **Template Convention**: A `renderResponse('Controller/Action')` call maps to `Resources/Private/Templates/Controller/Action.html`.

## Data Flow Patterns

Two main patterns are used to provide data to the Fluid templates. See the respective specification files for details.

1.  **InfoBox Pattern**: For status-like reports. See `.Specs/InfoBoxSystem.md`.
2.  **Direct Repository Pattern**: For list-based reports that query the `tx_mysqlreport_query_information` table. See `.Specs/Repositories.md`.

## Configuration (`ExtConf.php`)

-   **`slowQueryThreshold` (float)**: Used by the **Slow Queries** sub-module to filter queries by duration. It does not affect the logging process itself.

## Module Structure

### Main Module: `mysqlreport`

-   **Title**: MySQL Report
-   **Description**: A comprehensive suite of monitoring tools. It acts as a shell for the sub-modules.

---

### Sub-Module: `mysqlreport_status`

-   **Title**: System Status
-   **Controller**: `StatusController::indexAction`
-   **Data Flow**: Uses the **InfoBox Pattern**.
-   **Template**: `Resources/Private/Templates/Status/Index.html`

---

### Sub-Module: `mysqlreport_innodb`

-   **Title**: InnoDB Metrics
-   **Controller**: `InnoDBController::indexAction`
-   **Data Flow**: Uses the **InfoBox Pattern**.
-   **Template**: `Resources/Private/Templates/InnoDB/Index.html`

---

### Sub-Module: `mysqlreport_threadcache`

-   **Title**: Thread Cache
-   **Controller**: `ThreadCacheController::indexAction`
-   **Data Flow**: Uses the **InfoBox Pattern**.
-   **Template**: `Resources/Private/Templates/ThreadCache/Index.html`

---

### Sub-Module: `mysqlreport_tablecache`

-   **Title**: Table Cache
-   **Controller**: `TableCacheController::indexAction`
-   **Data Flow**: Uses the **InfoBox Pattern**.
-   **Template**: `Resources/Private/Templates/TableCache/Index.html`

---

### Sub-Module: `mysqlreport_querycache`

-   **Title**: Query Cache
-   **Controller**: `QueryCacheController::indexAction`
-   **Data Flow**: Uses the **InfoBox Pattern**.
-   **Template**: `Resources/Private/Templates/QueryCache/Index.html`

---

### Sub-Module: `mysqlreport_misc`

-   **Title**: General Metrics
-   **Controller**: `MiscController::indexAction`
-   **Data Flow**: Uses the **InfoBox Pattern**.
-   **Template**: `Resources/Private/Templates/Misc/Index.html`

---

### Sub-Module: `mysqlreport_filesort`

-   **Title**: Filesort Analysis
-   **Controllers**: `FileSortController::indexAction`, `ProfileController`
-   **Data Flow**: Uses the **Direct Repository Pattern** (`QueryInformationRepository::findQueryInformationRecordsWithFilesort()`).
-   **Template**: `Resources/Private/Templates/FileSort/Index.html`

---

### Sub-Module: `mysqlreport_fulltablescan`

-   **Title**: Full Table Scans
-   **Controllers**: `FullTableScanController::indexAction`, `ProfileController`
-   **Data Flow**: Uses the **Direct Repository Pattern** (`QueryInformationRepository::findQueryInformationRecordsWithFullTableScan()`).
-   **Template**: `Resources/Private/Templates/FullTableScan/Index.html`

---

### Sub-Module: `mysqlreport_slowquery`

-   **Title**: Slow Queries
-   **Controllers**: `SlowQueryController::indexAction`, `ProfileController`
-   **Data Flow**: Uses the **Direct Repository Pattern** (`QueryInformationRepository::findQueryInformationRecordsWithSlowQueries()`).
-   **Template**: `Resources/Private/Templates/SlowQuery/Index.html`

---

### Sub-Module: `mysqlreport_profile`

-   **Title**: Request-Based Profiling
-   **Controller**: `ProfileController` (multiple actions)
-   **Data Flow**: Uses the **Direct Repository Pattern**, calling various methods on `QueryInformationRepository`.
-   **Templates**:
    -   `listAction` -> `Resources/Private/Templates/Profile/List.html`
    -   `showAction` -> `Resources/Private/Templates/Profile/Show.html`
    -   `queryTypeAction` -> `Resources/Pvivate/Templates/Profile/QueryType.html`
    -   `infoAction` -> `Resources/Private/Templates/Profile/Info.html`
    -   `queryProfilingAction` -> `Resources/Private/Templates/Profile/QueryProfiling.html`
    -   `downloadAction` -> Generates a direct CSV response, no template.
