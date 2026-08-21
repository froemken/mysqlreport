..  include:: /Includes.rst.txt

..  _developer-manual:

================
Developer manual
================

`mysqlreport` uses the first available hook/event in TYPO3 to attach its own
SQL logger to the Doctrine system in TYPO3. Please keep in mind that therefore
all queries executed before that hook cannot be collected or analyzed.

With version 2.0.0 `mysqlreport` comes with an API to add your
own panels/infoboxes to the backend module.

Please see the API section for more information.

..  toctree::
    :maxdepth: 2
    :titlesonly:

    API/Index

