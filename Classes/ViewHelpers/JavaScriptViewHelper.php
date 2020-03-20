<?php
namespace Colorcube\Anfahrt\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

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
class JavaScriptViewHelper extends AbstractViewHelper
{

    use CompileWithContentArgumentAndRenderStatic;

    /**
     * Disable the escaping interceptor because otherwise the child nodes would be escaped before this view helper
     * can decode the text's entities.
     *
     * @var bool
     */
    protected $escapingInterceptorEnabled = false;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('value', 'mixed', 'Value to set');
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
        $variableProvider = $renderingContext->getVariableProvider();
        $allVariables = $variableProvider->getAll();

        $value = $arguments['value'];
        if ($value === null) {
            $content = $renderChildrenClosure();
        } else {
            $content = $value;
        }

        // this makes long keys first in list and first to be replaced in template
        $keys = array_map('strlen', array_keys($allVariables));
        array_multisort($keys, SORT_DESC, $allVariables);

        foreach ($allVariables as $key => $value) {
            $content = str_replace('$v_'.$key, $value, $content);
        }
        return $content;
    }
}
