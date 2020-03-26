<?php

namespace Colorcube\Anfahrt\Form\Element;

/**
 * This file is part of the "anfahrt" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * None element is a "disabled" input element with formatted values if needed.
 */
class GeoCodeMap extends AbstractFormElement
{
    /**
     * Default field information enabled for this element.
     *
     * @var array
     */
    protected $defaultFieldInformation = [
        'tcaDescription' => [
            'renderType' => 'tcaDescription',
        ],
    ];

    /**
     * This will render a non-editable display of the content of the field.
     *
     * @return array The HTML code for the TCEform field
     */
    public function render(): array
    {
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);

        $resultArray = $this->initializeResultArray();

        $parameterArray = $this->data['parameterArray'];
        $config = $parameterArray['fieldConf']['config'];

        $fieldInformationResult = $this->renderFieldInformation();
        $fieldInformationHtml = $fieldInformationResult['html'];
        $resultArray = $this->mergeChildReturnIntoExistingResult($resultArray, $fieldInformationResult, false);

//        DebuggerUtility::var_dump($this->data);
//        DebuggerUtility::var_dump($parameterArray);

        $addressFields = $this->getAdressFields($config);

        $dataAttr['data-lat-field'] = $this->getFieldBaseName($config['latField']);
        $dataAttr['data-lon-field'] = $this->getFieldBaseName($config['lonField']);
        $dataAttr['data-address-fields'] = json_encode($addressFields);
        $dataAttr['data-tiles'] = 'https://{s}.tile.openstreetmap.de/{z}/{x}/{y}.png';
        $dataAttr['data-attribution'] = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';

        $dataAtrributes = '';
        foreach ($dataAttr as $attr => $attrData) {
            $dataAtrributes .= " $attr=\"" . htmlspecialchars($attrData) . '"';
        }


        $html = [];
        $html[] = '<div class="formengine-field-item t3js-formengine-field-item">';
        $html[] = $fieldInformationHtml;
        $html[] = '<div class="form-wizards-wrap">';
        $html[] = '<div class="form-wizards-element">';
        $html[] = '<div class="form-control-wrap">';
        $html[] = '<div id="t3js-location-map-container" class="t3js-location-map-container" style="width: 100%; height: 35em" ' . $dataAtrributes . '></div>';
        $html[] = '<p class="help-block">' . $iconFactory->getIcon('overlay-info', Icon::SIZE_SMALL)->render() . ' ' . htmlspecialchars($this->getLanguageService()->sL('LLL:EXT:anfahrt/Resources/Private/Language/locallang.xlf:anfahrt.pi_flexform.be_drag_description')) . '</p>';
        if ($config['showAdressSearch']) {
            $html[] = '<input type="text" id="location-map-address" style="min-width:30em; width: 70%;" placeholder="' . htmlspecialchars($this->getLanguageService()->sL('LLL:EXT:anfahrt/Resources/Private/Language/locallang.xlf:anfahrt.pi_flexform.be_geocode_description')) . '" />';
            $html[] = '<input type="button" onclick="window.LeafletBackendAnfahrt.geoCodeFromInput(document.getElementById(\'location-map-address\').value); return false;" value="' . htmlspecialchars($this->getLanguageService()->sL('LLL:EXT:anfahrt/Resources/Private/Language/locallang.xlf:anfahrt.pi_flexform.be_go')) . '" />';
        } else if ($addressFields) {
            $html[] = '<input type="button" onclick="window.LeafletBackendAnfahrt.geoCodeFromAddress(); return false;" value="' . htmlspecialchars($this->getLanguageService()->sL('LLL:EXT:anfahrt/Resources/Private/Language/locallang.xlf:anfahrt.pi_flexform.be_go')) . '" />';
        }
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';


        $resultArray['html'] = implode(LF, $html);
        $resultArray['stylesheetFiles'][] = 'EXT:anfahrt/Resources/Public/Contrib/leaflet-core-1.7.0.css';
        $resultArray['requireJsModules'][] = 'TYPO3/CMS/Anfahrt/leaflet-core-1.7.0';
        $resultArray['requireJsModules'][] = 'TYPO3/CMS/Anfahrt/LeafletBackend';

        return $resultArray;
    }

    protected function getAdressFields($config)
    {
        $adressFields = new \stdClass();
        $hasProperties = false;

        if ($config['addressField']) {
            $adressFields->address = $this->getFieldBaseName($config['addressField']);
            $hasProperties = true;
        }
        if ($config['streetField']) {
            $adressFields->street = $this->getFieldBaseName($config['streetField']);
            $hasProperties = true;
        }
        if ($config['housenumberField']) {
            $adressFields->housenumber = $this->getFieldBaseName($config['housenumberField']);
            $hasProperties = true;
        }
        if ($config['zipField']) {
            $adressFields->zip = $this->getFieldBaseName($config['zipField']);
            $hasProperties = true;
        }
        if ($config['cityField']) {
            $adressFields->city = $this->getFieldBaseName($config['cityField']);
            $hasProperties = true;
        }
        if ($config['countryField']) {
            $adressFields->country = $this->getFieldBaseName($config['countryField']);
            $hasProperties = true;
        }

        return $hasProperties ? $adressFields : false;
    }

    protected function getFieldBaseName($fieldName)
    {
        $myFieldName = $this->data['flexFormFieldName'] ? $this->data['flexFormFieldName'] : $this->data['fieldName'];
        return str_replace($myFieldName, $fieldName, $this->data['parameterArray']['itemFormElName']);
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
