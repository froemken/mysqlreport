..  include:: /Includes.rst.txt

..  _upgrades:

========
Upgrades
========

..  _upgrade-5-0-0:

Upgrade to version 5.0.0
========================

Added TYPO3 14 compatibility.

Removed TYPO3 13 compatibility.

Added `adminpanel` as conflicting extension because of duplicate
SQL logging.

Removed PSR-14 event `ModifyQueryInformationRecordsEvent` and
`RoundDurationOfQueryInformationRecordsEventListener`. Duration formatting
is now handled in Fluid templates.

..  _upgrade-4-0-0:

Upgrade to version 4.0.0
========================

Please visit the Install Tool and execute "Database compare":

*   Add new column `using_index`
*   Remove old column `not_using_index`
*   Remove old column `profile`
*   Add table `tx_mysqlreport_query_information`
*   Remove table `tx_mysqlreport_domain_model_profile`

Make sure TYPO3 `adminpanel` is not installed. This system extension
interferes with our own SQL logger.

All `profile` or `profileRecord` variables were changed
to `queryInformationRecord`.

The event `ModifyProfileRecordsEvent` was renamed
to `ModifyQueryInformationRecordsEvent`.

All extension settings have new names. Please visit extension settings and adapt
them to your needs again. There is no Upgrade Wizard.

..  _upgrade-2-0-0:

Upgrade to version 2.0.0
========================

The array-based API was moved to `Services.yaml`. This is a major change which
will break on TYPO3 systems installed as Standalone (ZIP/TAR). Please remove
the old `mysqlreport` extension first and then install version 2.0.0
of `mysqlreport`. For Composer-based installations, this issue does not apply.

If you encounter an error while upgrading, please remove
the `typo3temp/var/cache/code/di/` folder and reload the page.

..  _upgrade-2-0-0-migrate-array-config:

Migrate array config to Services.yaml
-------------------------------------

If you have added your own info boxes to `mysqlreport`, you must migrate your
info boxes from array syntax in `MySqlReportInfoBoxes.php` to `Services.yaml`:

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

Please refer to the Developer API section to get a list of all allowed tags.

