<?php

namespace ServerKnights\SkNewsletterhelper\ContentObject;

use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;

class NewsletterCoreContentObject extends AbstractContentObject
{
    public function render($conf = []){
        $content = null;
        if (array_key_exists('html', $conf) && array_key_exists('html.', $conf)) {
            $content = $this->cObj->cObjGetSingle($conf['html'], $conf['html.']);
        }
        /** @var TypoScriptFrontendController $controller */
        $controller = $GLOBALS['TSFE'];
        /** @var ServerRequestInterface $request */
        $request = $GLOBALS['TYPO3_REQUEST'];

        // Render non-cached page parts by replacing placeholders which are taken from cache or added during page generation
        if ($controller->isINTincScript()) {
            if (!$controller->isGeneratePage()) {
                // When page was generated, this was already called. Avoid calling this twice.
                $controller->preparePageContentGeneration($request);
            }
            $contentBak = $controller->content;
            $controller->content = $content;
            $controller->INTincScript();
            $content = $controller->content;
            $controller->content = $contentBak;
            unset($contentBak);
        }
        return $content;
    }
}