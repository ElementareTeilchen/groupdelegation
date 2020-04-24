<?php
namespace In2code\Groupdelegation\ViewHelpers;

use TYPO3\CMS\Core\Core\Environment;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class InArrayViewHelper extends AbstractViewHelper
{

    /**
     * Initializes the arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('needle', 'string', 'String (aka needle) to look for in the array', true);
        $this->registerArgument('array', 'array', 'Array (aka haystack), where to look for the needle', true);
    }

    /**
     * Print icon html for $identifier key
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        return in_array($arguments['needle'], $arguments['haystack']);
    }
}
