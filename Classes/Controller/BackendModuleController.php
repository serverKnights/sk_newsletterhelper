<?php

namespace ServerKnights\SkNewsletterhelper\Controller;

use Psr\Http\Message\ResponseInterface;;
use ServerKnights\SkNewsletterhelper\Service\ExtentionConfigurationService;
use ServerKnights\SkNewsletterhelper\Service\TemplateService;
use TYPO3\CMS\Backend\Routing\PreviewUriBuilder;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Context\LanguageAspectFactory;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Directive;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Mutation;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\MutationCollection;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\MutationMode;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\PolicyRegistry;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\UriValue;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Http\ForwardResponse;

class BackendModuleController extends ActionController
{

    protected ModuleTemplateFactory $moduleTemplateFactory;
    protected readonly PageRenderer $pageRenderer;
    protected ExtentionConfigurationService $extentionConfigurationService;
    protected SiteFinder $siteFinder;
    protected PageRepository $pageRepository;
    protected IconFactory $iconFactory;
    protected TemplateService $templateService;
    protected PolicyRegistry $policyRegistry;
    public function __construct(
        PageRenderer $pageRenderer,
        ModuleTemplateFactory $moduleTemplateFactory,
        ExtentionConfigurationService $extentionConfigurationService,
        SiteFinder $siteFinder,
        PageRepository $pageRepository,
        IconFactory $iconFactory,
        PolicyRegistry $policyRegistry,
        TemplateService $templateService
    ) {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
        $this->pageRenderer = $pageRenderer;
        $this->extentionConfigurationService = $extentionConfigurationService;
        $this->extentionConfigurationService->init();
        $this->siteFinder = $siteFinder;
        $this->pageRepository = $pageRepository;
        $this->iconFactory = $iconFactory;
        $this->policyRegistry = $policyRegistry;
        $this->templateService = $templateService;
    }

    public function showStartButtonsAction(): ResponseInterface
    {
        $assignArray = [];
        $parsedBody = $this->request->getParsedBody();

        if($this->extentionConfigurationService->checkIfExtentionSettingsAreFilled()){ // Set the specific template file
            return new ForwardResponse('mainScreen');
        }else{
            // Load JavaScript via JavaScriptRenderer
            $this->pageRenderer->getJavaScriptRenderer()->addJavaScriptModuleInstruction(
                JavaScriptModuleInstruction::create('@serverKnights/sk-newsletterhelper/loadIcon.js')
            );
        }

        if(isset($parsedBody["checkAndSetNode"])){
            $assignArray = $this->extentionConfigurationService->checkAndSetNode();
        }

        if(isset($parsedBody["checkAndSetMjml"])){
            $assignArray = $this->extentionConfigurationService->checkAndSetMjml();
        }

        if(isset($parsedBody["installMJML"])){
            $assignArray = $this->extentionConfigurationService->installMjml();
        }

        $this->view->assignMultiple($assignArray);
        // Create the module template
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        // Render the content
        $moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($moduleTemplate->renderContent());
    }
    public function createTemplateAction(): ResponseInterface{

        $directoryPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath("sk_newsletterhelper")."/Resources/Private/Basetemplates";
        $htmlFiles = glob($directoryPath . '/*.html');
        if($this->templateService->isCustomTemplateSet()){
            $customDirectoryPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath(trim($this->templateService->getCustomExtensionName())).$this->templateService->getCustomTemplatePath();
            if(is_dir($customDirectoryPath)){
                $customHtmlFiles = glob($customDirectoryPath . '/*.html');
                $htmlFiles = array_merge($htmlFiles, $customHtmlFiles);
            }
        }

        foreach ($htmlFiles as $filePath) {
            $templates[] = [
                'name' => basename($filePath), // Extracts the filename from the path
                'path' => $filePath // Full path of the template file
            ];
        }

        $this->view->assign('templates', $templates);
        $this->view->assign('pageId', $this->request->getQueryParams()["id"]);
        return $this->htmlResponse();
    }

    public function mainScreenAction(): ResponseInterface{

        $pageArguments = $this->request->getAttribute('routing');
    //    $pageId = $pageArguments->getPageId();


        if(!empty($this->request->getQueryParams()["id"])){
            if(isset($this->request->getParsedBody()["template"]["path"])){
                $this->templateService->createTemplate($this->request->getQueryParams()["id"],$this->request->getParsedBody()["template"]["path"]);
            }
            if(!$this->templateService->hasTemplate($this->request->getQueryParams()["id"])){
                return new ForwardResponse('createTemplate');
            }
        }



        $languageService = $this->getLanguageService();
        $pageId = (int)($this->request->getQueryParams()['id'] ?? 0);
        $moduleData = $this->request->getAttribute('moduleData');
        $pageInfo = BackendUtility::readPageAccess($pageId, $this->getBackendUser()->getPagePermsClause(Permission::PAGE_SHOW));

        $view = $this->moduleTemplateFactory->create($this->request);
        $view->setBodyTag('<body class="typo3-module-viewpage">');
        $view->setModuleId('typo3-module-viewpage');
        $view->setTitle(
            $languageService->sL('LLL:EXT:viewpage/Resources/Private/Language/locallang_mod.xlf:mlang_tabs_tab'),
            $pageInfo['title'] ?? ''
        );

        if (!$this->isValidDoktype($pageId)) {
            $view->addFlashMessage(
                $languageService->sL('LLL:EXT:viewpage/Resources/Private/Language/locallang.xlf:noValidPageSelected'),
                '',
                ContextualFeedbackSeverity::INFO
            );
            return $view->renderResponse('Empty');
        }
        $previewLanguages = $this->getPreviewLanguages($pageId);
        if ($previewLanguages !== [] && $moduleData->clean('language', array_keys($previewLanguages))) {
            $this->getBackendUser()->pushModuleData($moduleData->getModuleIdentifier(), $moduleData->toArray());
        }
        $languageId = (int)$moduleData->get('language');
        $targetUri = PreviewUriBuilder::create($pageId)
            ->withAdditionalQueryParameters($this->getTypeParameterIfSet($pageId))
            ->withLanguage($languageId)
            ->withAdditionalQueryParameters(['isBackend' => "1"])
            ->buildUri();
        $targetUrl = (string)$targetUri;
        if ($targetUri === null || $targetUrl === '') {
            $view->addFlashMessage(
                $languageService->sL('LLL:EXT:viewpage/Resources/Private/Language/locallang.xlf:noSiteConfiguration'),
                '',
                ContextualFeedbackSeverity::WARNING
            );
            return $view->renderResponse('Empty');
        }

        $this->registerDocHeader($view, $pageId, $languageId, $targetUrl);
        $current = $moduleData->get('States')['current'] ?? [];
        $current['label'] = ($current['label'] ?? $languageService->sL('LLL:EXT:viewpage/Resources/Private/Language/locallang.xlf:custom'));
        $current['width'] = MathUtility::forceIntegerInRange($current['width'] ?? 320, 300);
        $current['height'] = MathUtility::forceIntegerInRange($current['height'] ?? 480, 300);

        $custom = $moduleData->get('States')['custom'] ?? [];
        $custom['width'] = MathUtility::forceIntegerInRange($custom['width'] ?? 320, 300);
        $custom['height'] = MathUtility::forceIntegerInRange($custom['height'] ?? 480, 300);

        $view->assignMultiple([
            'current' => $current,
            'custom' => $custom,
            'presetGroups' => $this->getPreviewPresets($pageId),
            'url' => $targetUrl,
        ]);

        if ($targetUri->getScheme() !== '' && $targetUri->getHost() !== '') {
            // temporarily(!) extend the CSP `frame-src` directive with the URL to be shown in the `<iframe>`
            $mutation = new Mutation(MutationMode::Extend, Directive::FrameSrc, UriValue::fromUri($targetUri));
            $this->policyRegistry->appendMutationCollection(new MutationCollection($mutation));
        }
        return $view->renderResponse('MainScreen');
    }

    /**
     * Get available presets for page id.
     */
    protected function getPreviewPresets(int $pageId): array
    {
        $presetGroups = [
            'desktop' => [],
            'tablet' => [],
            'mobile' => [],
            'unidentified' => [],
        ];
        $previewFrameWidthConfig = BackendUtility::getPagesTSconfig($pageId)['mod.']['web_view.']['previewFrameWidths.'] ?? [];
        foreach ($previewFrameWidthConfig as $item => $conf) {
            $data = [
                'key' => substr($item, 0, -1),
                'label' => $conf['label'] ?? null,
                'type' => $conf['type'] ?? 'unknown',
                'width' => (isset($conf['width']) && (int)$conf['width'] > 0 && !str_contains($conf['width'], '%')) ? (int)$conf['width'] : null,
                'height' => (isset($conf['height']) && (int)$conf['height'] > 0 && !str_contains($conf['height'], '%')) ? (int)$conf['height'] : null,
            ];
            $width = (int)substr($item, 0, -1);
            if (!isset($data['width']) && $width > 0) {
                $data['width'] = $width;
            }
            if (!isset($data['label'])) {
                $data['label'] = $data['key'];
            } else {
                $data['label'] = $this->getLanguageService()->sL(trim($data['label']));
            }

            if (array_key_exists($data['type'], $presetGroups)) {
                $presetGroups[$data['type']][$data['key']] = $data;
            } else {
                $presetGroups['unidentified'][$data['key']] = $data;
            }
        }

        return $presetGroups;
    }

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    /**
     * Returns the preview languages
     */
    protected function getPreviewLanguages(int $pageId): array
    {
        $languages = [];
        $modSharedTSconfig = BackendUtility::getPagesTSconfig($pageId)['mod.']['SHARED.'] ?? [];
        if (($modSharedTSconfig['view.']['disableLanguageSelector'] ?? false) === '1') {
            return $languages;
        }

        try {
            $site = $this->siteFinder->getSiteByPageId($pageId);
            $siteLanguages = $site->getAvailableLanguages($this->getBackendUser(), false, $pageId);

            foreach ($siteLanguages as $siteLanguage) {
                $languageAspectToTest = LanguageAspectFactory::createFromSiteLanguage($siteLanguage);
                $page = $this->pageRepository->getPageOverlay($this->pageRepository->getPage($pageId), $siteLanguage->getLanguageId());

                if ($this->pageRepository->isPageSuitableForLanguage($page, $languageAspectToTest)) {
                    $languages[$siteLanguage->getLanguageId()] = $siteLanguage->getTitle();
                }
            }
        } catch (SiteNotFoundException $e) {
            // do nothing
        }
        return $languages;
    }

    /**
     * Verifies if doktype of given page is valid - not a folder / recycler / ...
     */
    protected function isValidDoktype(int $pageId = 0): bool
    {
        if ($pageId === 0) {
            return false;
        }
        $page = BackendUtility::getRecord('pages', $pageId);
        $pageType = (int)($page['doktype'] ?? 0);
        return $pageType !== 0
            && !in_array($pageType, [
                PageRepository::DOKTYPE_SPACER,
                PageRepository::DOKTYPE_SYSFOLDER,
                PageRepository::DOKTYPE_RECYCLER,
            ], true);
    }



    /**
     * With page TS config it is possible to force a specific type id via mod.web_view.type for a page id or a page tree.
     * The method checks if a type is set for the given id and returns the additional GET string.
     */
    protected function getTypeParameterIfSet(int $pageId): string
    {
        $typeParameter = '';
        $typeId = (int)(BackendUtility::getPagesTSconfig($pageId)['mod.']['web_view.']['type'] ?? 0);
        if ($typeId > 0) {
            $typeParameter = '&type=' . $typeId;
        }
        return $typeParameter;
    }


    protected function registerDocHeader(ModuleTemplate $view, int $pageId, int $languageId, string $targetUrl)
    {
        $languageService = $this->getLanguageService();
        $languages = $this->getPreviewLanguages($pageId);
        if (count($languages) > 1) {
            $languageMenu = $view->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
            $languageMenu->setIdentifier('_langSelector');
            foreach ($languages as $value => $label) {
                $href = (string)$this->uriBuilder->buildUriFromRoute(
                    'page_preview',
                    [
                        'id' => $pageId,
                        'language' => (int)$value,
                    ]
                );
                $menuItem = $languageMenu->makeMenuItem()
                    ->setTitle($label)
                    ->setHref($href);
                if ($languageId === (int)$value) {
                    $menuItem->setActive(true);
                }
                $languageMenu->addMenuItem($menuItem);
            }
            $view->getDocHeaderComponent()->getMenuRegistry()->addMenu($languageMenu);
        }

        $buttonBar = $view->getDocHeaderComponent()->getButtonBar();
        $showButton = $buttonBar->makeLinkButton()
            ->setHref($targetUrl)
            ->setDataAttributes([
                'dispatch-action' => 'TYPO3.WindowManager.localOpen',
                'dispatch-args' => GeneralUtility::jsonEncodeForHtmlAttribute([
                    $targetUrl,
                    true, // switchFocus
                    'newTYPO3frontendWindow', // windowName,
                ]),
            ])
            ->setTitle($languageService->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.showPage'))
            ->setShowLabelText(true)
            ->setIcon($this->iconFactory->getIcon('actions-view-page', Icon::SIZE_SMALL));
        $buttonBar->addButton($showButton);

        // Get the Typo3 URI Builder
        $resetLayoutUrl = $this->uriBuilder->setTargetPageType(1708283669)
            ->setArguments(['tx_sknewsletterhelper_newsletterhelper[action]' => 'resetLayout',
                'tx_sknewsletterhelper_newsletterhelper[controller]' => 'NewsletterHelper'])
            ->buildFrontendUri();

        // Shortcut
        $saveButton = $buttonBar->makeLinkButton()
            ->setHref($resetLayoutUrl)
            ->setClasses('sk-newsletterhelper-save-button')
            ->setTitle("Change Layout")
            ->setShowLabelText(true)
            ->setIcon($this->iconFactory->getIcon('actions-refresh', Icon::SIZE_SMALL));
        $buttonBar->addButton($saveButton, ButtonBar::BUTTON_POSITION_RIGHT);
    }

    /**
     * Returns the shortcut title for the current page.
     */
    protected function getShortcutTitle(int $pageId): string
    {
        $pageTitle = '';
        $pageRow = BackendUtility::getRecord('pages', $pageId) ?? [];
        if ($pageRow !== []) {
            $pageTitle = BackendUtility::getRecordTitle('pages', $pageRow);
        }
        return sprintf(
            '%s: %s [%d]',
            $this->getLanguageService()->sL('LLL:EXT:viewpage/Resources/Private/Language/locallang_mod.xlf:mlang_labels_tablabel'),
            $pageTitle,
            $pageId
        );
    }
}