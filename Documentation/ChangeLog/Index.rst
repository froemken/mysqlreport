..  include:: /Includes.rst.txt

..  _changelog:

=========
ChangeLog
=========

..  contents::
    :local:

..  _changelog-5-1-0:

Version 5.1.0
=============

*   [TASK] Implement new ext and module icon specific for TYPO3 14.1
*   [TASK] Remove TYPO3 14.0 compatibility

..  _changelog-5-0-0:

Version 5.0.0
=============

*   [TASK] Add TYPO3 14 compatibility
*   [TASK] Remove TYPO3 13 compatibility

..  _changelog-4-0-2:

Version 4.0.2
=============

*   [TASK] Use specific connection for mysqlreport table

..  _changelog-4-0-1:

Version 4.0.1
=============

*   [TASK] Add logging for each try-catch

..  _changelog-4-0-0:

Version 4.0.0
=============

*   [TASK] Remove toolbar icon
*   [TASK] Replace placeholders in SQL to build real SQL for EXPLAIN
*   [TASK] Move EXPLAIN query in logger to destruct
*   [TASK] Add LoggingStatement to log bindValue and execute method
*   [TASK] Completely rework registering of logger
*   [TASK] Remove TYPO3 11 and TYPO3 12 compatibility
*   [TASK] Remove ConnectionHelper class
*   [TASK] Rename tx_mysqlreport_domain_model_profile to tx_mysqlreport_query_information

..  _changelog-3-0-4:

Version 3.0.4
=============

*   [BUGFIX] Update to correct TYPO3 version in composer.json

..  _changelog-3-0-3:

Version 3.0.3
=============

*   [BUGFIX] Allow installation of mysqlreport without installed dashboard

..  _changelog-3-0-2:

Version 3.0.2
=============

*   [DOCS] Add Screenshots

..  _changelog-3-0-1:

Version 3.0.1
=============

*   [DOCS] Reduce line length to max. 80 chars
*   [DOCS] Add a note about problems with "Add EXPLAIN" option
*   [TASK] Add status report about activated "Add EXPLAIN" option
*   [TASK] Add toolbar item to notify about activated "Add EXPLAIN" option

..  _changelog-3-0-0:

Version 3.0.0
=============

*   Remove TYPO3 10 compatibility
*   Add TYPO3 12 compatibility
*   [FEATURE] Implement dashboard widgets
*   [BUGFIX] Repair queries in ProfileRepository
*   [BUGFIX] Repair analyzing queries with FTS
*   [TASK] Apply new php-cs-fixer configuration
*   [DOCS] Set indents to 4 spaces

..  _changelog-2-1-0:

Version 2.1.0
=============

*   [FEATURE] Add Download Option for JSON and CSV
*   [BUGFIX] Further adjustments for queries with FULL GROUP
*   [TESTS] Remove PHP 7.3 tests

..  _changelog-2-0-4:

Version 2.0.4
=============

*   [BUGFIX] Incompatible queries in case of FULL GROUP

..  _changelog-2-0-3:

Version 2.0.3
=============

*   [DOCS] Use packagist package name as package name
*   [DOCS] Remove duplicate section

..  _changelog-2-0-2:

Version 2.0.2
=============

*   Use correct include in ChangeLog docs
*   Add space in admin manual to prevent broken parsing
*   Improve ChangeLog

..  _changelog-2-0-1:

Version 2.0.1
=============

*   Add upgrade section to documentation

..  _changelog-2-0-0:

Version 2.0.0
=============

*   Registration of InfoBoxes has moved from array syntax into :file:`Services.yaml`
*   PageFinder has been migrated to Symfony Service Locator
*   Add new SqlViewHelper to format SQL statements (only Composer)
*   Update documentation. Explain new API.
*   Update documentation. Add a note about using Admin Panel.
*   Remove all usages of GeneralUtility::makeInstance
*   Add infobox on empty results in Slow Query Log, Queries using filesort and FTS
*   Add Event to modify profile records
*   Add EventListener to reduce precision of duration to 6

..  _changelog-1-1-5:

Version 1.1.5
=============

*   Use :php:`->fetch()` instead of :php:`->fetchAssociative()` for TYPO3 v10
    standalone compatibility

..  _changelog-1-1-4:

Version 1.1.4
=============

*   Remove hard-coded version from :file:`ext_emconf.php`

..  _changelog-1-1-3:

Version 1.1.3
=============

*   Prevent division by zero at several places

..  _changelog-1-1-2:

Version 1.1.2
=============

*   Prevent division by zero
*   Solve undefined array key cachecmd while storing records in BE
*   Add method to replace query questionmarks

..  _changelog-1-1-1:

Version 1.1.1
=============

*   Set default value of profile to empty array
*   Set connection configuration for SqlLoggerHelper
*   Set values to NULL for DBAL type NULL

..  _changelog-1-1-0:

Version 1.1.0
=============

*   Use ExtConf instead of ExtensionConfiguration class
*   Do not log queries to our own profile table
*   Implement ConnectionHelper to prevent logging own queries
*   Implement SqlLoggerHelper for temporary deactivating the logger
*   Implement Profile model for better type safety
*   Add infobox for max_allowed_packet
*   Add infobox for temporary tables
*   Update infobox for table cache
*   Update infobox for table definition cache
*   Add new module for slow queries
*   Add new extension setting to set duration for slow queries
*   Add button for detail view instead of clicking on duration value
*   Add query profile view for filesort, notUsingIndex and slow query lists
*   Implement ProfileFactory to reduce method calls to environment values

..  _changelog-1-0-0:

Version 1.0.0
=============

*   Remove TYPO3 v9 compatibility
*   Add TYPO3 v11 compatibility
*   This version is still TYPO3 v10 compatible
*   No new features, but completely rewritten code
*   Much better API to register new Panels/InfoBoxes for BE module

