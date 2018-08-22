<?php
namespace Colorcube\Anfahrt\ViewHelpers;



use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;

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
class InlineJsonViewHelper extends AbstractViewHelper implements CompilableInterface
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


        $content = preg_replace ( '/\r\n|\r|\n/', '', $content);

        return json_encode($content);
    }
}
