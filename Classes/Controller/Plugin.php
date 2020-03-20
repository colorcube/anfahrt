<?php
namespace Colorcube\Anfahrt\Controller;


/**
 * This file is part of the "anfahrt" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;


/**
 * Plugin 'Google Maps' for the 'anfahrt' extension.
 *
 * @author René Fritz <r.fritz@colorcube.de>
 */
class Plugin
{

    /**
     * The current cObject
     *
     * Note: This must be public cause it is set by ContentObjectRenderer::callUserFunction()
     *
     * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     */
    public $cObj;


    function main($content, $conf)
    {

        $view = GeneralUtility::makeInstance(StandaloneView::class , $this->cObj);
        $view->setTemplateRootPaths(array(GeneralUtility::getFileAbsFileName($conf['view.']['templateRootPath'])));
        $view->setFormat('html');

        $this->pi_initPIflexForm();

        $settings = $conf['settings.'];

        $variables = [];

        $variables['id'] = $this->cObj->data['uid'];

        $width = trim($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'width', 'description'));
        $variables['width'] = $width ? $width : $settings['width'];
        $height = trim($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'height', 'description'));
        $variables['height'] = $height ? $height : $settings['height'];
        $zoom = (int)($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'zoom', 'description'));
        $variables['zoom'] = $zoom ? $zoom : $settings['zoom'];

        $variables['latitude'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'latitude', 'sDEF');
        $variables['longitude'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'longitude', 'sDEF');
        $variables['address'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'address', 'description');
        $variables['address_json'] = json_encode($variables['address']);

        $variables['description'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'text', 'description');

        switch ($settings['mapProvider']) {
            case 'google':
                $googleMapApiKey = $settings['googleApiKey'] ? $settings['googleApiKey'] : \Colorcube\Anfahrt\Utility\EmConfiguration::get('googleMapApiKey');
                if (!$googleMapApiKey) {
                    return '<div>Google Maps API key not configured in extension configuration of \'anfahrt\' extension!</div>';
                }
                $variables['googleApiKey'] = $googleMapApiKey;
                $view->setTemplate('Google/Anfahrt');
                break;
            case 'leaflet':
                $variables['leafletTilesUrl'] = $settings['leafletTilesUrl'];
                $variables['leafletAttribution'] = $settings['leafletAttribution'];
                $view->setTemplate('Leaflet/Anfahrt');
                break;

            default:
                return '<div>settings.mapProvider not configured in TypoScript setup of \'anfahrt\' extension!</div>';
                break;
        }

        $view->assignMultiple($variables);
        return $view->render();
    }



    /*******************************
     *
     * FlexForms related functions
     *
     *******************************/

    /**
     * Converts $this->cObj->data['pi_flexform'] from XML string to flexForm array.
     *
     * @param string $field Field name to convert
     */
    public function pi_initPIflexForm($field = 'pi_flexform')
    {
        // Converting flexform data into array:
        if (!is_array($this->cObj->data[$field]) && $this->cObj->data[$field]) {
            $this->cObj->data[$field] = GeneralUtility::xml2array($this->cObj->data[$field]);
            if (!is_array($this->cObj->data[$field])) {
                $this->cObj->data[$field] = [];
            }
        }
    }

    /**
     * Return value from somewhere inside a FlexForm structure
     *
     * @param array $T3FlexForm_array FlexForm data
     * @param string $fieldName Field name to extract. Can be given like "test/el/2/test/el/field_templateObject" where each part will dig a level deeper in the FlexForm data.
     * @param string $sheet Sheet pointer, eg. "sDEF
     * @param string $lang Language pointer, eg. "lDEF
     * @param string $value Value pointer, eg. "vDEF
     * @return string|NULL The content.
     */
    public function pi_getFFvalue($T3FlexForm_array, $fieldName, $sheet = 'sDEF', $lang = 'lDEF', $value = 'vDEF')
    {
        $sheetArray = is_array($T3FlexForm_array) ? $T3FlexForm_array['data'][$sheet][$lang] : '';
        if (is_array($sheetArray)) {
            return $this->pi_getFFvalueFromSheetArray($sheetArray, explode('/', $fieldName), $value);
        }
        return null;
    }

    /**
     * Returns part of $sheetArray pointed to by the keys in $fieldNameArray
     *
     * @param array $sheetArray Multidimensiona array, typically FlexForm contents
     * @param array $fieldNameArr Array where each value points to a key in the FlexForms content - the input array will have the value returned pointed to by these keys. All integer keys will not take their integer counterparts, but rather traverse the current position in the array an return element number X (whether this is right behavior is not settled yet...)
     * @param string $value Value for outermost key, typ. "vDEF" depending on language.
     * @return mixed The value, typ. string.
     * @access private
     * @see pi_getFFvalue()
     */
    public function pi_getFFvalueFromSheetArray($sheetArray, $fieldNameArr, $value)
    {
        $tempArr = $sheetArray;
        foreach ($fieldNameArr as $k => $v) {
            if (MathUtility::canBeInterpretedAsInteger($v)) {
                if (is_array($tempArr)) {
                    $c = 0;
                    foreach ($tempArr as $values) {
                        if ($c == $v) {
                            $tempArr = $values;
                            break;
                        }
                        $c++;
                    }
                }
            }
            else {
                $tempArr = $tempArr[$v];
            }
        }
        return $tempArr[$value];
    }
}
