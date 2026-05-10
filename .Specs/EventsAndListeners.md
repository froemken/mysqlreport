# Events and Listeners

This document specifies the PSR-14 events and listeners used within the extension. They serve two primary purposes: internal data modification and integration with the TYPO3 Core.

---

## Internal Event: `ModifyQueryInformationRecordsEvent`

-   **Location**: `Classes/Event/ModifyQueryInformationRecordsEvent.php`
-   **Purpose**: This event is dispatched to allow for the modification of query information records after they have been fetched from the database but before they are returned to the caller.
-   **Data**:
    -   `methodName`: The name of the repository method that dispatched the event.
    -   `queryInformationRecords`: An array of associative arrays, each representing a database record.
-   **Design Note**: The event intentionally lacks a global `set...` method for the records. Instead, it provides `updateQueryInformationRecord(int $key, array $record)`, forcing listeners to modify records in place. This prevents a listener from accidentally discarding the entire result set.
-   **Dispatching Location**: This event is dispatched in most read-methods of the `QueryInformationRepository`.

### Listener: `RoundDurationOfQueryInformationRecordsEventListener`

-   **Location**: `Classes/EventListener/RoundDurationOfQueryInformationRecordsEventListener.php`
-   **Purpose**: To ensure data consistency for the `duration` field.
-   **Functionality**:
    -   Listens to the `ModifyQueryInformationRecordsEvent`.
    -   Iterates through all records in the event.
    -   If a `duration` field exists, it formats its value to a float with exactly 6 decimal places.
    -   It then updates the record in the event object.

---

## TYPO3 Core Integration: Caching

### Listener: `CacheAction`

-   **Location**: `Classes/EventListener/CacheAction.php`
-   **Purpose**: To provide a simple way for backend users to truncate the query log table, which can grow very large.
-   **Functionality**: The class has two distinct roles:
    1.  **Event Listener**: It listens to TYPO3's `ModifyClearCacheActionsEvent`. The `__invoke` method adds a new custom button to the "Clear Cache" menu in the TYPO3 backend. This button is configured to trigger a cache command named `mysqlprofiles`.
    2.  **Cache Processor**: The `clearProfiles()` method is registered in `ext_localconf.php` as a cache clearing processor. It is executed by the TYPO3 Core when a cache command is issued. It checks if the command is `mysqlprofiles` and, if so, executes a `TRUNCATE` statement on the `tx_mysqlreport_query_information` table.
