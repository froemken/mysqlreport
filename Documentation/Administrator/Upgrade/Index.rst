..  include:: /Includes.rst.txt


..  _upgrades:

========
Upgrades
========

Upgrade to version 4.0.0
========================

Please visit and execute "DB compare" to add new column "using_index" and
remove old column "not_using_index".

Please visit and execute "DB compare" to remove old column "profile".

Make sure TYPO3 "adminpanel" is not installed. This system extension
interferes with our own SQL logger.

Upgrade to version 2.0.0
========================

I have moved the array based API to Services.yaml. This is a huge change which
will break on TYPO3 systems installed as Standalone (ZIP/TAR). Please remove
the old `mysqlreport` extension first and then install the new 2.0.0 version
of `mysqlreport`. For composer based installations I could not reproduce this
error.

If you get an error while upgrading, please remove
the `typo3temp/var/cache/code/di/` folder and reload your page.

Migrate array config to Services.yaml
-------------------------------------

If you have added your own infoboxes to `mysqlreport` you have to migrate your
infoboxes from array syntax in `MySqlReportInfoBoxes.php` to `Services.yaml`:

Old version:

..  code-block:: php

    return [
        'aUniqueName' => [
            'class' => \[YourVendor]\[YourExtKey]\InfoBox\Overview\AbortedConnectsInfoBox::class,
            'pageIdentifier' => 'overview'
        ]
    ];

New version:

..  code-block:: yaml

    services:
      ...
      [YourVendor]\[YourExtKey]\InfoBox\Overview\AbortedConnectsInfoBox:
        tags:
          - name: 'mysqlreport.infobox.overview'
            priority: 60

Please have a look into the Developer API to get a list of all allowed tags.
