..  include:: /Includes.rst.txt


..  _configuration:

=============
Configuration
=============

..  _extensionSettings:

Extension Settings
==================

Head over to the `Settings` module of TYPO3 in the left menu of TYPO3 backend
and choose `Configure Extensions`. Open the accordion for `mysqlreport`.

enableFrontendLogging
---------------------

Activate logging for frontend requests

`mysqlreport` will only collect the queries with their execution times and
query type (SELECT/INSERT/UPDATE/DELETE).

enableBackendLogging
--------------------

Activate logging for backend requests

`mysqlreport` will only collect the queries with their execution times and
query type (SELECT/INSERT/UPDATE/DELETE).

activateExplainQuery
--------------------

`mysqlreport` executes each query again
with a prefixed `EXPLAIN` to retrieve detailed index and performance
information. You will see these additional information in backend module
of `mysqlreport` in section `Profiling` in the detail view of a selected query.

..  note::

    If this feature is activated it can slow down your TYPO3 system a lot.
    Please keep that in mind and activate that options for some minutes or
    few hours, but not days or weeks. The additional data can grow very fast
    and can exceed your DB storage very fast. A size of over 6 GB is not
    seldom!


slowQueryThreshold
------------------

`mysqlreport` can NOT read the original queries from Slow Query Log of your
server. But, as we already have collected the duration of all queries, we can
show the first 100 queries with a duration higher than the configured value
here (Default: 10.0 seconds).

If you want to see queries slower than slowQueryThreshold you have to activate one
of these profile checkboxes from above.
