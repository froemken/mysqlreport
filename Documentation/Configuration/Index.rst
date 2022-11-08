.. include:: /Includes.rst.txt


.. _configuration:

=============
Configuration
=============

.. _extensionSettings:

Extension Settings
==================

Head over to the `Settings` module of TYPO3 in the left menu of TYPO3 backend and choose
`Configure Extensions`. Open the accordion for `mysqlreport`.

profileFrontend
---------------

Activate profiling for frontend requests

profileFrontend
---------------

Activate profiling for frontend requests

`mysqlreport` will only collect the queries with there execution times and query type (SELECT/INSERT/UPDATE/DELETE).

profileBackend
--------------

Activate profiling for backend requests

`mysqlreport` will only collect the queries with there execution times and query type (SELECT/INSERT/UPDATE/DELETE).

addExplain
----------

After activating this setting `mysqlreport` will start an additional profiling of each individual query.
It collects the timings of each step (Query Cache, Authentication, collect data) the database server needs to
execute the query internally.
Further `mysqlreport` executes each query again with a prefixed `EXPLAIN` to retrieve detailed index and performance
information.
You will see these additional information in backend module of `mysqlreport` in section `Profiling` in the detail
view of a selected query.

If this feature is activated it can slow down your TYPO3 system a lot. Please keep that in mind and activate
that options for some minutes or hours, but not days or weeks. The additional data can grow very fast and can
exceed your DB storage very fast. A size of over 6 GB is not seldom!

slowQueryTime
-------------

`mysqlreport` can NOT read the original queries from Slow Query Log of your server. But, as we already have
collected the duration of all queries, we can show the first 100 queries with a duration higher than the configured
value here (Default: 10.0 seconds).

If you want to see queries slower that slowQueryTime you have to activate one of these profile checkboxes from above.
