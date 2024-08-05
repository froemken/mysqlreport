# MySQL Report

[![Latest Stable Version](https://poser.pugx.org/stefanfroemken/mysqlreport/v/stable.svg)](https://packagist.org/packages/stefanfroemken/mysqlreport)
[![TYPO3 13.2](https://img.shields.io/badge/TYPO3-13.2-green.svg)](https://get.typo3.org/version/13)
[![License](https://poser.pugx.org/stefanfroemken/mysqlreport/license)](https://packagist.org/packages/stefanfroemken/mysqlreport)
[![Total Downloads](https://poser.pugx.org/stefanfroemken/mysqlreport/downloads.svg)](https://packagist.org/packages/stefanfroemken/mysqlreport)
[![Monthly Downloads](https://poser.pugx.org/stefanfroemken/mysqlreport/d/monthly)](https://packagist.org/packages/stefanfroemken/mysqlreport)
![Build Status](https://github.com/froemken/mysqlreport/actions/workflows/typo3_13.yml/badge.svg)

With `mysqlreport` you can analyze and profile all SQL queries created by
`ConnectionPool` and Doctrine `QueryBuilder`.

After installation, you should visit the extension configuration in extension manager.
Here you can activate logging for FE and/or BE queries.
With an additional checkbox you can activate on-detail profiling for each query.
Be careful with this last checkbox. It may slow down your system drastically.

After saving extension configuration you will find a new TYPO3 module in Backend.
Switch between the different reports via selecting them with selectbox at top.

Happy reading and analyzing

Please deactivate logging after analyzing. Else you will flood your database
with a really huge amount of analyzing data. I have added you a
new entry in Clear-Cache menu to truncate all profiling data.

## Sponsors

Big THX goes to [Blackfire.io](https://www.blackfire.io)
