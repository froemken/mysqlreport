.. include:: /Includes.rst.txt


.. _installation:

============
Installation
============

Extension Manager
=================

Install `mysqlreport` in TYPO3 standalone installations with help of Extension Manager:

..  rst-class:: bignums

1. TYPO3 BE Login

   Login into TYPO3 backend as an administrator or system maintainer.

2. Visit Extension Manager

   Click `Extension Manager` from the left side menu.

3. Get Extension

   Choose `Get Extensions` from the upper selectbox.

4. Search

   Use the search mask to search for `mysqlreport`.

5. Install

   Find `mysqlreport` in the list and install the extension with the icon on the left.

5. Wait

   The installation will be confirmed with a blue notification on the upper right.

Composer
========

Install `mysqlreport` in TYPO3 composer based installations on the shell:

..  rst-class:: bignums

1. Shell Login

   Login into shell of your TYPO3 installation.

2. Change Directory

   Move into the root directory of your TYPO3 installation

3. Install

   .. code-block:: bash

      composer req stefanfroemken/mysqlreport

4. Add `mysqlreport` profile table

   .. code-block:: bash

      vendor/bin/typo3 extension:setup -e mysqlreport


Next step
=========

Configure `mysqlreport` in :ref:`Extension Settings <extensionSettings>`.
