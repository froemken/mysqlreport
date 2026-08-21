..  include:: /Includes.rst.txt

..  _admin-manual:

====================
Administrator manual
====================

After installation of `mysqlreport` you will see a new backend
module `MySQL Report` in the :guilabel:`System` section of the left menu.

Choose a section from one of the topics.

..  _admin-views:

Views
=====

..  _admin-overview:

Overview
--------

This is the first view you will see after opening the `mysqlreport` backend
module. It contains general information about your MySQL/MariaDB server and
uptime.

..  _admin-query-cache:

Query Cache
-----------

Shows general information about the Query Cache of your server.

..  _admin-innodb:

InnoDB
------

Shows general information about the InnoDB status like InnoDB Buffer Pool Size
of your server.

..  _admin-threads-cache:

Threads Cache
-------------

Shows general information about the Threads Cache of your server.

..  _admin-table-cache:

Table Cache
-----------

Shows general information about the Table and Table Definitions Cache of
your server.

..  _admin-profiling:

Profiling
---------

This view is empty by default. Please visit extension settings first and
activate your preferred profile method.

After visiting some pages in the backend and/or frontend this view will show you a grouped
selection of all requests.

Click on the duration to show all queries of the selected request grouped
by type (SELECT/INSERT/DELETE).

Click on the duration to show all queries of a given request and type ordered
from slowest to fastest query.

Click again on the duration of a query to show the full query with its placeholders.

If you have activated `activateExplainQuery` in extension settings you will see further
information about your query like timings about each step the server needs to
collect the data for your query. Furthermore, you will see an `EXPLAIN` output of your
query.

..  _admin-queries-using-filesort:

Queries using filesort
----------------------

Sorting a lot of huge queries will exceed the amount of RAM usage and will
start a sorting based on your hard disk or SSD. This is always a lot slower
than sorting data in RAM. It should be prevented as best as possible. Use this
view to show all queries which initiated a file sort.

..  _admin-queries-with-fts:

Queries with FTS
----------------

If a table does not have sufficient indexes defined, it may happen that
the server has to search the full table one row after the other which costs a lot
of time. Please check the list of queries here to identify queries without a
defined index or where a given index could not be used.

..  _admin-slow-queries:

Slow Queries
------------

We have implemented this feature with version 1.1.0. Please activate
profiling and set `slowQueryThreshold` in extension settings to your needs. After some
queries have been logged, this view will show you the 100 slowest queries, if there
are any slower than configured.

..  hint::

    `mysqlreport` cannot access the original Slow Query Log of your server.
    This view is just a result of a query of our profile table.

..  _admin-clear-profiles:

Clear Profiles
==============

After activating profiling in extension settings each query for each activated
request will be stored in table `tx_mysqlreport_query_information`. As large
as your TYPO3 instance may be, this table can grow very fast and can slow down your
TYPO3 system a lot. Please keep an eye on that table.
`mysqlreport` comes with a new entry in the :guilabel:`Clear Cache` menu of
TYPO3 (top right menu) called :guilabel:`Clear MySQL Profiles`. A click will
clear (TRUNCATE) the complete profile table.

..  toctree::
    :maxdepth: 2
    :titlesonly:

    Upgrade/Index

