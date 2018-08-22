<?php
namespace Colorcube\Anfahrt\ViewHelpers;

/*                                                                        *
 * This script is backported from the TYPO3 Flow package "TYPO3.Fluid".   *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;

/**
 * Outputs an argument/value without any escaping. Is normally used to output
 *
 * variables will be replaced with $v_ prefixed:
 *
 *  <cc:javaScript>
 *  <script>
 *  function initialize_anfahrt_map$v_id() {
 *     var myLatlng = new google.maps.LatLng($v_latitude, $v_longitude);
 *     var mapOptions = {
 *        zoom: $v_zoom,
 *        center: myLatlng
 *     };
 *  }
 *  </script>
 *  </cc:javaScript>
 *
 *
 * @author René Fritz <r.fritz@colorcube.de>
 */
class JavaScriptViewHelper extends AbstractViewHelper implements CompilableInterface
{
    /**
     * Disable the escaping interceptor because otherwise the child nodes would be escaped before this view helper
     * can decode the text's entities.
     *
     * @var bool
     */
    protected $escapingInterceptorEnabled = false;

    /**
     * @param mixed $value The value to output
     * @return string
     */
    public function render($value = null)
    {
        return static::renderStatic(
            [
                'value' => $value
            ],
            $this->buildRenderChildrenClosure(),
            $this->renderingContext
        );
    }

    /**
     * Render children without escaping
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $allVariables = $renderingContext->getTemplateVariableContainer()->getAll();

        $value = $arguments['value'];
        if ($value === null) {
            $content = $renderChildrenClosure();
        } else {
            $content = $value;
        }

        // this makes long keys first in list an first to be replaced in template
        $keys = array_map('strlen', array_keys($allVariables));
        array_multisort($keys, SORT_DESC, $allVariables);

        foreach ($allVariables as $key => $value) {
            $content = str_replace('$v_'.$key, $value, $content);
        }
        return $content;
    }
}
