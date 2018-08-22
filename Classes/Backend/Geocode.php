<?php
namespace Colorcube\Anfahrt\Backend;

/**
 * This file is part of the "anfahrt" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LocalizationFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Backend forms map display and geo coding function
 *
 * @author René Fritz <r.fritz@colorcube.de>
 */
class Geocode
{

    public function geoCodeLatitude($PA, $fobj)
    {
        return '<input class="formField2" name="' . $PA['itemFormElName'] . '" value="' . htmlspecialchars($PA['itemFormElValue']) . '" id="hidden_latitude" />';
    }

    public function geoCodeLongitude($PA, $fobj)
    {
        return '<input class="formField2" name="' . $PA['itemFormElName'] . '" value="' . htmlspecialchars($PA['itemFormElValue']) . '" id="hidden_longitude" />';
    }

    public function geoCodeAddress($PA, $fobj)
    {
        $LL = $this->includeLocalLang();

        return '
            <form action="#">
            <input type="text" name="' . $PA['itemFormElName'] . '" id="address" value="' . htmlspecialchars($PA['itemFormElValue']) . '" style="min-width:30em; width: 70%;" />
            <input type="button" onclick="codeAddress(document.getElementById(\'address\').value)" value="' . $GLOBALS['LANG']->getLLL('anfahrt.pi_flexform.be_go', $LL) . '" />
            <p class="help-block">' . htmlspecialchars($GLOBALS['LANG']->getLLL('anfahrt.pi_flexform.be_geocode_description', $LL)) . '</p>';
    }

    public function geoCodeMap($config)
    {
        $LL = $this->includeLocalLang();
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);

        $googleMapApiKey = \Colorcube\Anfahrt\Utility\EmConfiguration::get('googleMapApiKey');
        if (!$googleMapApiKey) {
            return '<div style="color:red">Google Maps API key not configured in extension configuration of \'anfahrt\' extension!</div>';
        }

        // Read Flexform
        $flexFormContent = $config['row']['pi_flexform'];

        $longitudeFlex = (is_array($flexFormContent)) ? $flexFormContent['data']['sDEF']['lDEF']['longitude']['vDEF'] : '0';
        $latitudeFlex = (is_array($flexFormContent)) ? $flexFormContent['data']['sDEF']['lDEF']['latitude']['vDEF'] : '0';
        $address = (is_array($flexFormContent)) ? $flexFormContent['data']['sDEF']['lDEF']['be_search']['vDEF'] : '';

        // TODO the map is not displayed in the right size when the tab (with map on it) is opened
        // seems that some calculations goes wrong when the map div is not visible
        // don't know how to fix that

        // Draggable Function thanks to Grzegorz Banka (grzegorz@grzegorzbanka.com)
        $content = '
          <div id="map" style="min-width:400px width:100%; max-width:100%; height: 400px"></div>
          <p class="help-block">' . $iconFactory->getIcon('overlay-info', Icon::SIZE_SMALL)->render() . ' ' .htmlspecialchars($GLOBALS['LANG']->getLLL('anfahrt.pi_flexform.be_drag_description', $LL)) . '</p>
          </form><br />

          <script async defer src="../typo3conf/ext/anfahrt/Resources/Public/JavaScript/BackendGeocode.js" type="text/javascript"></script>
          <script async defer src="https://maps.googleapis.com/maps/api/js?key='.$googleMapApiKey.'&callback=initialize_anfahrt_map" type="text/javascript"></script>
          <script type="text/javascript">
            function initialize_anfahrt_map() {
                anfahrt_map(' . $latitudeFlex . ', ' . $longitudeFlex . ', ' . json_encode($address) . ');
            }
          </script>';

        return "" . $content . "";
    }

    protected function includeLocalLang()
    {
        $languageFilePath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('anfahrt') . 'Resources/Private/Language/locallang.xlf';
        /** @var $languageFactory LocalizationFactory */
        $languageFactory = GeneralUtility::makeInstance(LocalizationFactory::class);
        // Read the strings in the required charset (since TYPO3 4.2)
        $LOCAL_LANG = $languageFactory->getParsedData($languageFilePath, $GLOBALS['LANG']->lang, 'utf-8');

        return $LOCAL_LANG;
    }
}

