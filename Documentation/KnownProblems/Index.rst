.. include:: /Includes.rst.txt


.. _known-problems:

==============
Known Problems
==============

Support of further Database Systems
===================================

Currently, `mysqlreport` just supports MySQL and MariaDB only.

Support of further Connections
==============================

Currently, `mysqlreport` just supports the DEFAULT Database Connection of TYPO3 only.

SQL Logger
==========

`mysqlreport` comes with its own SQL Logger. If you have implemented your own SQL Logger
`mysqlreport` will overwrite SQL Logger with its own version.

Missing Queries in profiling
============================

`mysqlreport` is not a TYPO3 system extension. That's why `mysqlreport` has to wait until the first official hook
or event to inject its own SQL Logger into the TYPO3 system. All queries before that event can therefore not be logged.
