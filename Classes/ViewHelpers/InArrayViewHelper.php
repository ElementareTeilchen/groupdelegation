<?php

declare(strict_types=1);

namespace ElementareTeilchen\Groupdelegation\ViewHelpers;

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
        $this->registerArgument('haystack', 'array', 'Array (aka haystack), where to look for the needle', true);
    }

    /**
     * Print icon html for $identifier key
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return bool
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        return in_array($arguments['needle'], $arguments['haystack']);
    }
}
