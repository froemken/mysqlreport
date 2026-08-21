..  include:: /Includes.rst.txt

..  _known-problems:

==============
Known Problems
==============

..  _known-problems-adminpanel:

Admin Panel
===========

If you have installed the TYPO3 system extension `adminpanel`, `mysqlreport` can still be used, but
collecting DB queries and profiling information will not work, as
`adminpanel` registers its own SQL logger and overwrites the SQL logger
of `mysqlreport`. Please uninstall `adminpanel` if you need the full feature
set of `mysqlreport`.

..  _known-problems-typo3-compatibility:

TYPO3 compatibility
===================

Try-catch blocks are placed around methods to prevent throwing exceptions or
breaking TYPO3 while using `mysqlreport`. This extension is ready for live
systems. If you encounter a problem, please create an issue at
`MySQL Report on GitHub <https://github.com/froemken/mysqlreport/issues>`__.

..  _known-problems-support-of-further-database-systems:

Support of further Database Systems
===================================

Currently, `mysqlreport` supports MySQL and MariaDB with drivers
`mysqli` and `pdo_mysql` only.

..  _known-problems-support-of-further-connections:

Support of further Connections
==============================

Currently, `mysqlreport` supports the default database connection of
TYPO3 only.

..  _known-problems-sql-logger:

SQL Logger
==========

`mysqlreport` comes with its own SQL logger. If you have implemented your own
SQL logger, `mysqlreport` will overwrite the SQL logger with its own version.

..  _known-problems-missing-queries-in-profiling:

Missing Queries in profiling
============================

`mysqlreport` is not a TYPO3 system extension. Therefore `mysqlreport` has to
wait until the first official hook or event to inject its own SQL logger into
the TYPO3 system. All queries executed before that event can therefore not be logged.

