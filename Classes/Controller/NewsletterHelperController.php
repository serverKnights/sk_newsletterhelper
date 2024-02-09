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
        //$this->view->setLayoutPathAndFilename('EXT:cf_cookiemanager_uikit/Resources/Private/Layouts/TestLayout.html');
        //$this->view->setLayoutPathAndFilename('EXT:sk_newsletterhelper/Resources/Private/Layouts/Newsletter.html');

        return $this->htmlResponse();
    }
}
