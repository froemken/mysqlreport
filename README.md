# MySQL Report

[![Latest Stable Version](https://poser.pugx.org/stefanfroemken/mysqlreport/v/stable.svg)](https://packagist.org/packages/stefanfroemken/mysqlreport)
[![TYPO3 14.3](https://img.shields.io/badge/TYPO3-14.3-green.svg)](https://get.typo3.org/version/14)
[![License](https://poser.pugx.org/stefanfroemken/mysqlreport/license)](https://packagist.org/packages/stefanfroemken/mysqlreport)
[![Total Downloads](https://poser.pugx.org/stefanfroemken/mysqlreport/downloads.svg)](https://packagist.org/packages/stefanfroemken/mysqlreport)
[![Monthly Downloads](https://poser.pugx.org/stefanfroemken/mysqlreport/d/monthly)](https://packagist.org/packages/stefanfroemken/mysqlreport)
![Build Status](https://github.com/froemken/mysqlreport/actions/workflows/ci.yml/badge.svg)

With `mysqlreport` you can analyze and profile all SQL queries created by
`ConnectionPool` and Doctrine `QueryBuilder`.

After installation, visit the extension configuration in the Extension Manager.
Here you can activate logging for FE and/or BE queries.
With an additional checkbox you can activate detailed profiling for each query.
Be careful with this last checkbox: it may slow down your system drastically.

After saving the extension configuration you will find a new TYPO3 module in the backend.
Switch between the different reports by selecting them with the select box at the top.

Happy reading and analyzing!

Please deactivate logging after analyzing. Otherwise, you will flood your database
with a huge amount of profiling data. There is a
new entry in the Clear Cache menu to truncate all profiling data.

## Sponsors

Big THX goes to [Blackfire.io](https://www.blackfire.io)
