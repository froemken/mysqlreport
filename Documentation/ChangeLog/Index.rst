..  include:: /Includes.rst.txt


..  _changelog:

=========
ChangeLog
=========

..  contents::
    :local:

Version 3.0.0
=============

*   Remove TYPO3 10 compatibility
*   Add TYPO3 12 compatibility
*   [FEATURE] Implement a lot of dashboard widgets
*   [BUGFIX] Repair queries in ProfileRepository
*   [BUGFIX] Repair analyzing queries with FTS
*   [TASK] Apply new php-cs-fixer configuration
*   [DOCS] Set indents to 4 spaces

Version 2.1.0
=============

*   [FEATURE] Add Download Option for JSON and CSV
*   [BUGFIX] Further adjustments for queries with FULL GROUP
*   [TESTS] Remove PHP 7.3 tests

Version 2.0.4
=============

*   [BUGFIX] Incompatible queries in case of FULL GROUP

Version 2.0.3
=============

*   [DOCS] Use packagist package name as package name
*   [DOCS] Remove duplicate section

Version 2.0.2
=============

*   Use correct include in ChangeLog docs
*   Add space in admin manual to prevent broken parsing
*   Improve ChangeLog

Version 2.0.1
=============

*   Add upgrade section to documentation

Version 2.0.0
=============

*   Registration of InfoBoxes has moved from array syntax into :file:`Services.yaml`
*   PageFinder has been migrated to Symfony Service Locator
*   Add new SqlViewHelper to format SQL statements (only Composer)
*   Update documentation. Explain new API.
*   Update documentation. Add a note about using AdminPanel.
*   Remove all usages of GeneralUtility::makeInstance
*   Add infobox on empty results in Slow Query Log, Queries using filesort and FTS
*   Add Event to modify profile records
*   Add EventListener to reduce precision of duration to 6

Version 1.1.5
=============

*   Use :php:`->fetch()` instead of :php:`->fetchAssociative()` for TYPO3 v10 standalone compatibility

Version 1.1.4
=============

*   Remove hard-coded version from :file:`ext_emconf.php`

Version 1.1.3
=============

*   Prevent division by zero at several places

Version 1.1.2
=============

*   Prevent division by zero
*   Solve undefined array key cachecmd while storing records in BE
*   Add method to replace query questionmarks

Version 1.1.1
=============

*   Set default value of profile to empty array
*   Set connection configuration for SqlLoggerHelper
*   Set values to NULL for DBAL type NULL

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

Version 1.0.0
=============

*   Remove TYPO3 v9 compatibility
*   Add TYPO3 v11 compatibility
*   This version is still TYPO3 v10 compatible
*   No new features, but completely rewritten code
*   Much better API to register new Panels/InfoBoxes for BE module
