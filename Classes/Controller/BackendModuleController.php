<?php

namespace ServerKnights\SkNewsletterhelper\Controller;


use mysql_xdevapi\Exception;
use Psr\Http\Message\ResponseInterface;
use ServerKnights\SkNewsletterhelper\Service\VerifyService;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class BackendModuleController extends ActionController
{

    protected ModuleTemplateFactory $moduleTemplateFactory;
    protected PageRenderer $pageRenderer;
    protected VerifyService $verifyService;

    public function __construct(PageRenderer $pageRenderer, ModuleTemplateFactory $moduleTemplateFactory, VerifyService $verifyService) {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
        $this->pageRenderer = $pageRenderer;
        $this->verifyService = $verifyService;

    }

    public function showStartButtonsAction(): ResponseInterface
    {
        $extensionConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ExtensionConfiguration::class);

        $extentionNodePath = $extensionConfiguration->get('sk_newsletterhelper', 'NodePath');
        $extentionMjmlPath = $extensionConfiguration->get('sk_newsletterhelper', 'NodeModulesPath');

        $parsedBody = $this->request->getParsedBody();

        if(isset($parsedBody["checkAndSetNode"])){
            $nodePath = $this->verifyService->checkNode();

            if(!empty($nodePath) && $this->isValidPath($nodePath)){
                $this->view->assign('isNodePresent', true);
                $this->view->assign('nodePath', $nodePath);
                $extensionConfiguration->set('sk_newsletterhelper', ["NodePath" => $nodePath, "NodeModulesPath" => $extentionMjmlPath]);
            }else{
                $extensionConfiguration->set('sk_newsletterhelper', ["NodePath" => null, "NodeModulesPath" => $extentionMjmlPath]);
                $this->view->assign('isNodePresent', false);
            }
        }

        if(isset($parsedBody["checkAndSetMjml"])){
            $mjmlPath = $this->verifyService->checkMjml();

            if(!empty($mjmlPath) && $this->isValidPath($mjmlPath)){
                $this->view->assign('isMjmlPresent', true);
                $this->view->assign('mjmlPath', $mjmlPath);
                $extensionConfiguration->set('sk_newsletterhelper', ["NodePath" => $extentionNodePath, "NodeModulesPath" => $mjmlPath]);
            }else{
                $extensionConfiguration->set('sk_newsletterhelper', ["NodePath" => $extentionNodePath, "NodeModulesPath" => null]);
                $this->view->assign('isMjmlPresent', false);
            }
        }

        if(isset($parsedBody["installMJML"])){
            $targetDirName = 'sk_newsletterhelper';
            // Find the position of the target directory in the path
            $pos = strpos(__DIR__, $targetDirName);
            // Extract the path up to and including the target directory
            $baseDir = substr(__DIR__, 0, $pos + strlen($targetDirName));
            //$command = 'find . -name "mjml*"';
            $command = 'cd '.$baseDir.'; composer install';
            // Execute the command
            exec($command, $output, $returnStatus);

            $mjmlPath = $this->verifyService->checkMjml();

            if(!empty($mjmlPath) && $this->isValidPath($mjmlPath)){
                $this->view->assign('isMjmlPresent', true);
                $this->view->assign('mjmlPath', $mjmlPath);
                $extensionConfiguration->set('sk_newsletterhelper', ["NodePath" => $extentionNodePath, "NodeModulesPath" => $mjmlPath]);
            }else{
                $extensionConfiguration->set('sk_newsletterhelper', ["NodePath" => $extentionNodePath, "NodeModulesPath" => null]);
                $this->view->assign('isMjmlPresent', false);
            }
        }


        if(!empty($extentionNodePath) && !empty($extentionMjmlPath)){
            // Set the specific template file
            $this->view->setTemplatePathAndFilename("EXT:sk_newsletterhelper/Resources/Private/Templates/BackendModule/MainScreen.html");
        }else{









        }

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

    private function isValidPath($path) {
        // Check if the path exists
        if (file_exists($path)) {
            // Check if it's a directory or file
            if (is_dir($path) || is_file($path)) {
                return true;
            }
        }
        return false;
    }

}