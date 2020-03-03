.. include:: ../Includes.txt


.. _admin-manual:

Administrator Manual
====================

Installation
------------

There are two ways to properly install the extension.

1. Composer installation
^^^^^^^^^^^^^^^^^^^^^^^^

In case you use Composer to manage dependencies of your TYPO3 project,
you can just issue the following Composer command in your project root directory.

.. code-block:: bash

	composer require colorcube/anfahrt

2. Installation with Extension Manager
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Download and install the extension with the extension manager module.


Google Maps API Key
^^^^^^^^^^^^^^^^^^^

You need an api key which has to be set in the extension configuration in the extension manager.

For further information about Google Maps API Keys you may have a look here: https://developers.google.com/maps/documentation/javascript/get-api-key


Configuration
-------------

TypoScript
^^^^^^^^^^

Include the extension TypoScript in your project (e.g. in template record).

Some values will be used from TypoScript as default values if not set in flexform:

::

    plugin.tx_anfahrt_pi1 {
        settings {
            width = 100%
            height = 38rem
            zoom = 14
        }
    }



TSconfig
^^^^^^^^

The **Width**, **Height** and **Zoom** fields could be hidden for editors using TSconfig. The settings from TypoScript
are used then.

::

    TCEFORM.tt_content.pi_flexform.anfahrt_pi1 {
    	description {
    		width.disabled = 1
    		height.disabled = 1
    		zoom.disabled = 1
    	}
    }

