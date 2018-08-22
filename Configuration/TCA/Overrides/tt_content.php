<?php
defined('TYPO3_MODE') || die();

/**
 * Register Plugin and flexform
 */


$pluginSignature = 'anfahrt_pi1';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(array(
    'LLL:EXT:anfahrt/Resources/Private/Language/locallang.xlf:tt_content.list_type_pi1',
    $pluginSignature,
    'EXT:anfahrt/Resources/Public/Icons/Extension.svg'
),'list_type', 'anfahrt');

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'recursive,select_key,pages,recursive';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature,
    'FILE:EXT:anfahrt/Configuration/FlexForms/PluginFlexform.xml');