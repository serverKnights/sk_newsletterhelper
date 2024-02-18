<?php

declare(strict_types=1);

namespace ServerKnights\SkNewsletterhelper\Controller;


use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;

/**
 * This file is part of the "New" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2023 Bicha Stefan
 */

/**
 * NewsletterHelperController
 */
class NewsletterHelperController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * newsletterHelperRepository
     *
     * @var \ServerKnights\SkNewsletterhelper\Domain\Repository\NewsletterHelperRepository
     */
    protected $newsletterHelperRepository = null;

    /**
     * @param \ServerKnights\SkNewsletterhelper\Domain\Repository\NewsletterHelperRepository $newsletterHelperRepository
     */
    public function injectNewsletterHelperRepository(\ServerKnights\SkNewsletterhelper\Domain\Repository\NewsletterHelperRepository $newsletterHelperRepository)
    {
        $this->newsletterHelperRepository = $newsletterHelperRepository;
    }

    /**
     * action list
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAction(): \Psr\Http\Message\ResponseInterface
    {
        // Get the Typo3 URI Builder
        $this->uriBuilder->setCreateAbsoluteUri(true);
        $this->uriBuilder->setTargetPageType(1707673083);
        $generatedSavingUrl = $this->uriBuilder->uriFor(
            "save",
            null, // Controller arguments, if any
            "NewsletterHelper",
            "SkNewsletterhelper",
            "Newsletterhelper"
        );


        $configuratorTypes = [
            "save" => [
                "url" => $generatedSavingUrl,
                "method" => "save"
            ],
        ];

        $this->view->assignMultiple([
            'configuratorTypes' => base64_encode(json_encode($configuratorTypes)),
            'pageId' => $this->request->getAttribute('routing')->getPageId(),
        ]);


        return $this->htmlResponse();
    }

    public function saveAction(): \Psr\Http\Message\ResponseInterface
    {
        if($this->request->getMethod() === "POST"){
            $templateService = GeneralUtility::makeInstance(\ServerKnights\SkNewsletterhelper\Service\TemplateService::class);
            $pageArguments = $this->request->getAttribute('routing');
            $pageId = $pageArguments->getPageId();
            $content = $this->request->getBody()->getContents();
            $result = $templateService->saveTemplate($pageId,$content);
        }
        return $this->htmlResponse();
    }

    public function resetLayoutAction(): \Psr\Http\Message\ResponseInterface
    {
        $templateService = GeneralUtility::makeInstance(\ServerKnights\SkNewsletterhelper\Service\TemplateService::class);
        $pageArguments = $this->request->getAttribute('routing');
        $pageId = $pageArguments->getPageId();
        $result = $templateService->removeTemplate($pageId);
        return $this->htmlResponse();
    }
}
