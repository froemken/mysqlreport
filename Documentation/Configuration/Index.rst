..  include:: /Includes.rst.txt

..  _configuration:

=============
Configuration
=============

..  _extension-settings:

Extension Settings
==================

Head over to the :guilabel:`Admin Tools > Settings` module of TYPO3 in the left menu of the TYPO3 backend
and choose :guilabel:`Configure Extensions`. Open the accordion for `mysqlreport`.

..  _conf-enable-frontend-logging:

enableFrontendLogging
---------------------

Activate logging for frontend requests.

`mysqlreport` will collect the queries with their execution times and
query type (SELECT/INSERT/UPDATE/DELETE).

..  _conf-enable-backend-logging:

enableBackendLogging
--------------------

Activate logging for backend requests.

`mysqlreport` will collect the queries with their execution times and
query type (SELECT/INSERT/UPDATE/DELETE).

..  _conf-activate-explain-query:

activateExplainQuery
--------------------

`mysqlreport` executes each query again
with a prefixed `EXPLAIN` to retrieve detailed index and performance
information. You will see this additional information in the backend module
of `mysqlreport` in section :guilabel:`Profiling` in the detail view of a selected query.

..  note::

    If this feature is activated it can slow down your TYPO3 system a lot.
    Please keep that in mind and activate that option for a few minutes or
    hours, but not days or weeks. The additional data can grow very fast
    and can exceed your DB storage very fast. A size of over 6 GB is not
    uncommon!

..  _conf-slow-query-threshold:

slowQueryThreshold
------------------

`mysqlreport` cannot read the original queries from the Slow Query Log of your
server. However, as we already collect the duration of all queries, we can
show the first 100 queries with a duration higher than the configured value
here (default: 10.0 seconds).

If you want to see queries slower than `slowQueryThreshold`, you must activate one
of the logging options above (`enableFrontendLogging` or `enableBackendLogging`).

