.. include:: ../Includes.txt


.. _changelog:

=========
ChangeLog
=========

**Version 2.0.0**

Registration of InfoBoxes has moved from array syntax into Services.yaml
PageFinder has been migrated to Symfony Service Locator
Add new SqlViewHelper to format SQL statements (only composer)
Update documentation

**Version 1.1.5**

Use ->fetch() instead of ->fetchAssociative() for TYPO3 10 standalone compatibility

**Version 1.1.4**

Remove hard-coded version from ext_emconf.php

**Version 1.1.3**

Prevent division by zero at several places

**Version 1.1.2**

Prevent division by zero
Solve undefined array key cachecmd while storing records in BE
Add method to replace query questionmarks

**Version 1.1.1**

Set default value of profile to empty array
Set connection configuration for SqlLoggerHelper
Set values to NULL for DBAL type NULL

**Version 1.1.0**

Use ExtConf instead of ExtensionConfiguration class
Do not log queries to our own profile table
Implement ConnectionHelper to prevent logging own queries
Implement SqlLoggerHelper for temporary deactivating the logger
Implement Profile model for better type safety
Add infobox for max_allowed_packet
Add infobox for temporary tables
Update infobox for table cache
Update infobox for table definition cache
Add new module for slow queries
Add new extension setting to set duration for slow queries
Add button for detail view instead of clicking on duration value
Add query profile view for filesort, notUsingIndex and slow query lists
Implement ProfileFactory to reduce method calls to environment values

**Version 1.0.0**

Remove TYPO3 9 compatibility
Add TYPO3 11 compatibility
This version is still TYPO3 10 compatible
No new features, but completely rewritten code
Much better API to register new Panels/InfoBoxes for BE module
