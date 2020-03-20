<?php
defined('TYPO3_MODE') or die();

$boot = function () {

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43(
        'anfahrt', 'Classes/Controller/Plugin.php', '_pi1', 'list_type', 1);


    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:anfahrt/Configuration/TSconfig/ContentElementWizard.t3s">');

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1584264566] = array(
        'nodeName' => 'geoCodeMap',
        'priority' => '70',
        'class' => \Colorcube\Anfahrt\Form\Element\GeoCodeMap::class,
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals'][\Colorcube\Anfahrt\Evaluation\LatitudeEvaluation::class] = '';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals'][\Colorcube\Anfahrt\Evaluation\LongitudeEvaluation::class] = '';
};

$boot();
unset($boot);
