..  include:: /Includes.rst.txt


..  _developer-manual:

Developer manual
================

`mysqlreport` used the first available hook/event of TYPO3 to attach its own SQL logger to the Doctrine
system in TYPO3. Please keep in mind that therefore all queries before that hook could not be collected/analyzed.

With version 2.0.0 `mysqlreport` comes with a completely new API to add your own panels/infoboxes to backend module.
Please see `API` for more information.

..  toctree::
    :maxdepth: 2
    :titlesonly:

    API/Index
