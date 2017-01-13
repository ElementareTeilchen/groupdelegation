<?php
namespace In2code\Groupdelegation\ViewHelpers;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * In Array
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class InArrayViewHelper extends AbstractViewHelper
{

    /**
     * @param string $needle
     * @param array $haystack
     * @return bool
     */
    public function render(string $needle, array $haystack): bool
    {
        return in_array($needle, $haystack);
    }

}
