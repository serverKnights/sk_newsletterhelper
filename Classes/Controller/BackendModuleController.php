<?php

namespace ServerKnights\SkNewsletterhelper\Controller;


use mysql_xdevapi\Exception;
use Psr\Http\Message\ResponseInterface;;
use ServerKnights\SkNewsletterhelper\Service\ExtentionConfigurationService;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class BackendModuleController extends ActionController
{

    protected ModuleTemplateFactory $moduleTemplateFactory;
    protected readonly PageRenderer $pageRenderer;
    protected ExtentionConfigurationService $extentionConfigurationService;
    public function __construct(PageRenderer $pageRenderer, ModuleTemplateFactory $moduleTemplateFactory, ExtentionConfigurationService $extentionConfigurationService) {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
        $this->pageRenderer = $pageRenderer;
        $this->extentionConfigurationService = $extentionConfigurationService;
        $this->extentionConfigurationService->init();
    }

    public function showStartButtonsAction(): ResponseInterface
    {
        $assignArray = [];
        $parsedBody = $this->request->getParsedBody();

        if($this->extentionConfigurationService->checkIfExtentionSettingsAreFilled()){ // Set the specific template file
            $this->view->setTemplatePathAndFilename("EXT:sk_newsletterhelper/Resources/Private/Templates/BackendModule/MainScreen.html");
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


    public function mainScreenAction(): ResponseInterface{
        // Create the module template
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        // Render the content
        $moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($moduleTemplate->renderContent());
    }

}