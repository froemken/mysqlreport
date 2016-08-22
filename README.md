MySQL Report
============

This TYPO3 Extension analyzes and can profile all SQL queries created by $GLOBALS['TYPO3'].

After installation you should visit the extension configuration in extension manager.
Here you can activate logging for FE and/or BE queries.
With an additional checkbox you can activate on-detail profiling for each query.
Be careful with this last checkbox. It may slow down your system drastically.

After saving extension configuration you will find a new TYPO3 module in Backend.
Switch between the different reports via selecting them with selectbox at top.

Happy reading and analyzing

Please deactivate logging after analyzing. Else you will flood your database
with a really huge amount of analyzing data. I have added you a
new entry in Clear-Cache menu to truncate all profiling data.
