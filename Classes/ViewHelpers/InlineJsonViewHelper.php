<?php
namespace Colorcube\Anfahrt\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * Outputs an argument/value
 *
 * - without any escaping
 * - newlines are removed
 * - content will be json encoded
 *
 * use for popup window data in Google Maps for example
 *
 *
 * @author René Fritz <r.fritz@colorcube.de>
 */
class InlineJsonViewHelper extends AbstractViewHelper
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
        $value = $arguments['value'];
        if ($value === null) {
            $content = $renderChildrenClosure();
        } else {
            $content = $value;
        }


        $content = preg_replace ( '/\r\n|\r|\n/', '', $content);

        return json_encode($content);
    }
}
