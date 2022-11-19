.. include:: /Includes.rst.txt


.. _api:

================
MySQL Report API
================

Since `mysqlreport` 2.0.0 there is a completely rewritten API to add your own panels/infoboxes
to the backend module.

The infoboxes will be realized with the `f:be.infobox` ViewHelper of TYPO3.

Infobox Registry
================

If you want to extend pages of `mysqlreport` with further infoboxes you have to add
some lines to the Service.yaml of your extension.

Example content of the Service.yaml:

.. code-block:: yaml

   services:
     ...
     YourVendor\YourExtKey\InfoBox\Information\ConnectionInfoBox:
       tags:
         - name: 'mysqlreport.infobox.[pageIdentifier]'
           priority: 60

As you see, each infobox in the backend module has its own PHP class.
Please attach the tag name for the page you want to modify, to each of your infobox classes.
With `priority` you can change the loading order of the infoboxes on a specific page.

Currently, following tags are available:

*  mysqlreport.infobox.information
*  mysqlreport.infobox.innodb
*  mysqlreport.infobox.misc
*  mysqlreport.infobox.query_cache
*  mysqlreport.infobox.table_cache
*  mysqlreport.infobox.thread_cache

Your own Infobox
================

Create a new PHP class which extends the `AbstractInfoBox` class of `mysqlreport`:

.. code-block:: php

   <?php

   declare(strict_types=1);

   namespace StefanFroemken\Mysqlreport\InfoBox\Overview;

   use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
   use StefanFroemken\Mysqlreport\Menu\Page;

   class AbortedConnectsInfoBox extends AbstractInfoBox
   {
       protected $pageIdentifier = 'overview';

       protected $title = 'Aborted Connects';

       public function renderBody(Page $page): string
       {
           if (!isset($page->getStatusValues()['Aborted_connects'])) {
               $this->shouldBeRendered = false;
               return '';
           }

           $content = [];
           $content[] = 'You have %d aborted connects.';

           return sprintf(
               implode(' ', $content),
               $page->getStatusValues()['Aborted_connects']
           );
       }
   }

You have to set the `pageIdentifier` to show your infobox on the right view in backend module.

Set a `title`. It will be used as the title in the infobox.

The content of the infobox has to be rendered in the `renderBody` method. Over the `$page` argument
you have access to all the status and variable values of your MySQL/MariaDB server.

.. hint::

   Do not add HTML tags into your content within `renderBody` method. The content will be passed through
   `htmlspecialchars`. Please apply a Pull Request to extend the API or use your own template (see below).

Highlight Infobox
=================

The `AbstractInfoBox` class comes with a `setState` method which allows values from `StateEnumeration`.

*  empty value: default color: gray
*  -2 or StateEnumeration::STATE_NOTICE
*  -1 or StateEnumeration::STATE_INFO
*  0 or StateEnumeration::STATE_OK
*  1 or StateEnumeration::STATE_WARNING
*  2 or StateEnumeration::STATE_ERROR

Unordered List
==============

Sometimes it is useful to show some values of your server as list.
The list will be shown at the bottom of the infobox.

.. code-block:: php

   // Results in <ul><li>Value is OK</li></ul>
   $this->addUnorderedListEntry('Value is OK');

   // Results in <ul><li><strong>Aborted connects:</strong> 25</li></ul>
   $this->addUnorderedListEntry($page->getStatusValues()['Aborted_connects'], 'Aborted connects');

Disable Infobox
===============

If a server variable is not available for a server, it may help to hide the infobox in backend module.

Use `$this->shouldBeRendered = false;` in `renderBody` of your class.

Use own Template
================

By default `mysqlreport` uses the template from:

`EXT:mysqlreport/Resources/Private/Templates/InfoBox/Default.html`

Use `template` property to define your own template for rendering of your infobox:

.. code-block:: php

   protected $template = 'EXT:[your_ext_key]/Resources/Private/Templates/InfoBox/MyBetterTemplate.html'
