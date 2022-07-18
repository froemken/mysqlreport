# MySQL Report

![Build Status](https://github.com/froemken/mysqlreport/workflows/CI/badge.svg)

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
