<?php

namespace Colorcube\Anfahrt\Utility;

/**
 * This file is part of the "anfahrt" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Utility class to get the settings from Extension Manager
 *
 * (borrowed from news extension)
 */
class EmConfiguration
{
    private static $settings = null;

    /**
     * return the extension settings for a given key
     *
     * @return mixed
     */
    public static function get($key)
    {
        self::parseSettings();
        return static::$settings[$key];
    }

    /**
     * return the extension settings.
     *
     * @return array
     */
    public static function getSettings()
    {
        return self::parseSettings();
    }

    /**
     * Parse settings and return it as array
     *
     * @return array unserialized extconf settings
     */
    protected static function parseSettings()
    {
        if (!is_array(static::$settings)) {
            $settings = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['anfahrt']);

            if (!is_array($settings)) {
                $settings = [];
            }
            static::$settings = $settings;
        }
        return static::$settings;
    }
}
