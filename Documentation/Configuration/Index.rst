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

profileFrontend
---------------

Activate profiling for frontend requests

`mysqlreport` will only collect the queries with their execution times and
query type (SELECT/INSERT/UPDATE/DELETE).

profileBackend
--------------

Activate profiling for backend requests

`mysqlreport` will only collect the queries with their execution times and
query type (SELECT/INSERT/UPDATE/DELETE).

addExplain
----------

After activating this setting `mysqlreport` will start an additional profiling
of each individual query. It collects the timings of each
step (Query Cache, Authentication, collect data) the database server needs to
execute the query internally. Further `mysqlreport` executes each query again
with a prefixed `EXPLAIN` to retrieve detailed index and performance
information. You will see these additional information in backend module
of `mysqlreport` in section `Profiling` in the detail view of a selected query.

..  note::

    If this feature is activated it can slow down your TYPO3 system a lot.
    Please keep that in mind and activate that options for some minutes or
    few hours, but not days or weeks. The additional data can grow very fast
    and can exceed your DB storage very fast. A size of over 6 GB is not
    seldom!

..  warning::

    With help of the integrated SqlLogger for Doctrine the additional
    `SHOW profile;` query will be executed immediatly after the original query.
    As that additional query does not have an autoincrement, it will reset
    the internal `mysqli` information of the last query. That means: If
    the PHP code needs the `insert_id` after an INSERT or `affected_rows`
    after an UPDATE query, these infomation are lost (0). F.E. saving
    new scheduler tasks will throw an exception.


slowQueryTime
-------------

`mysqlreport` can NOT read the original queries from Slow Query Log of your
server. But, as we already have collected the duration of all queries, we can
show the first 100 queries with a duration higher than the configured value
here (Default: 10.0 seconds).

If you want to see queries slower that slowQueryTime you have to activate one
of these profile checkboxes from above.
