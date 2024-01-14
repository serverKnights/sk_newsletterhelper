<?php

namespace ServerKnights\SkNewsletterhelper\Controller;


use mysql_xdevapi\Exception;
use Psr\Http\Message\ResponseInterface;
use ServerKnights\SkNewsletterhelper\Service\VerifyService;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
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
        $parsedBody = $this->request->getParsedBody();

        if(isset($parsedBody["checkNPM"])){
            $isNpmPresent = $this->verifyService->checkNpm();

            if(!empty($isNpmPresent) && $this->isValidPath($isNpmPresent)){
                $this->view->assign('isNpmPresent', true);
                $this->view->assign('npmPath', $isNpmPresent);
            }else{
                $this->view->assign('isNpmPresent', false);
            }
        }
        if(isset($parsedBody["checkMJML"])){
            $isMjmlPresent = $this->verifyService->checkMjml();

            if(!empty($isMjmlPresent) && $this->isValidPath($isMjmlPresent)){
                $this->view->assign('isMjmlPresent', true);
                $this->view->assign('mjmlPath', $isMjmlPresent);
            }else{
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
            shell_exec($command);
        }


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