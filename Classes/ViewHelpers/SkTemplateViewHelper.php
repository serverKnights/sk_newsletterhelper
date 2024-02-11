<?php
declare(strict_types=1);

namespace ServerKnights\SkNewsletterhelper\ViewHelpers;

use ScssPhp\ScssPhp\Formatter\Debug;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

final class SkTemplateViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('pageId', 'integer', 'The Page Id', true);
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {
        $templateService = GeneralUtility::makeInstance(\ServerKnights\SkNewsletterhelper\Service\TemplateService::class);
        return file_get_contents($templateService->tenplatePath . "/" . $arguments['pageId'] . "/template.html");
    }
}