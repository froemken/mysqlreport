.. include:: /Includes.rst.txt


.. _admin-manual:

====================
Administrator manual
====================

After installation of `mysqlreport` you will see a new backend module `MySQL Report` in `System` section
of the left menu.

You can navigate throw multiple views over the upper left selectbox.

Views
=====

Overview
--------

This is the first view you will see after opening the `mysqlreport`backend module. It contains general information
about your MySQL/MariaDB server and uptime.

Query Cache
-----------

Shows general information about the Query Cache of your server.

InnoDB
------

Shows general information about the InnoDB status like InnoDB Buffer Pool Size of your server.

Threads Cache
-------------

Shows general information about the Threads Cache of your server.

Table Cache
-----------

Shows general information about the Table and Table Definitions Cache of your server.

Profiling
---------

This view is empty by default. Please visit extension settings before and activate your preferred
profile method.

After visiting some pages in BE and/or FE this view will show you a grouped selection of all requests.

Click on the `duration` to show all queries of selected request grouped by type (SELECT/INSERT/DELETE).

Click on the `duration` to show all queries of given request and type ordered from lowest to fastest query.

Click again on `duration` of a query to show full query with its placeholders.

If you have activated `addExplain` in extension settings you will see further information about your query
like timings about each step the server needs to collect the data for your query. Further you will
see an EXPLAIN output of your query.

Queries using filesort
----------------------

Sorting a lot of hugh queries will exceed the amount of RAM usage and will start a sorting based on your hard-disk
or SSD. This is always a lot slower than sorting data in RAM. It should be prevented as good as you can.
Use this view to show all queries which initiated a file sort.

Queries with FTS
----------------

If a table does not have sufficient indexes (INDEX) defined, it may happen that the server has to search full
table one row after the other which costs a lot of time.
Please check the list of queries here to identify queries without a defined index or, where a given index could
not be used.

Clear Profiles
==============

After activating profiling in extension settings each query for each activated request will be stored in
table `tx_mysqlreport_domain_model_profile`. As big as your TYPO3 could be, this table can grow very fast and can
slow down your TYPO3 system a lot. Please keep an eye on that table.
`mysqlreport` comes with a new entry in Clear Cache menu of TYPO3 (icon at the upper right)
called `Clear MySQL Profiles`. A click will clear (TRUNCATE) the complete profile table.
