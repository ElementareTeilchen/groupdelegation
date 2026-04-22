<?php

declare(strict_types=1);

namespace ElementareTeilchen\Groupdelegation\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class InArrayViewHelper extends AbstractViewHelper
{
    /**
     * Initializes the arguments
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('needle', 'string', 'String (aka needle) to look for in the array', true);
        $this->registerArgument('haystack', 'array', 'Array (aka haystack), where to look for the needle', true);
    }

    public function render(): bool
    {
        return in_array($this->arguments['needle'], $this->arguments['haystack']);
    }
}
