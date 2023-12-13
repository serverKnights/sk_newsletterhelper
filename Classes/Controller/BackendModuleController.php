<?php

namespace ServerKnights\SkNewsletterhelper\Controller;


use Psr\Http\Message\ResponseInterface;
use ServerKnights\SkNewsletterhelper\Service\VerifyNpmService;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class BackendModuleController extends ActionController
{

    protected ModuleTemplateFactory $moduleTemplateFactory;
    protected PageRenderer $pageRenderer;
    protected VerifyNpmService $verifyNpmService;

    public function __construct(PageRenderer $pageRenderer,ModuleTemplateFactory $moduleTemplateFactory,VerifyNpmService $verifyNpmService) {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
        $this->pageRenderer = $pageRenderer;
        $this->verifyNpmService = $verifyNpmService;

    }

    public function showStartButtonsAction(): ResponseInterface
    {

        if($this->request->hasArgument('isNpmPresent')){
            $isNpmPresent = $this->request->getArgument('isNpmPresent');
            if(!empty($isNpmPresent) && $this->isValidPath($isNpmPresent)){
                $this->view->assign('isNpmPresent', true);
                $this->view->assign('npmPath', $isNpmPresent);
            }else{
                $this->view->assign('isNpmPresent', false);
            }

        }


        // Create the module template
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        // Render the content
        $moduleTemplate->setContent($this->view->render());
        $this->view->assign('url', 'Hallo Welt');
        return $this->htmlResponse($moduleTemplate->renderContent());

    }

    public function verifyNPMAction(){
        $isNpmPresent = $this->verifyNpmService->checkNpm();

        $this->redirect('showStartButtons',null,null,['isNpmPresent' => $isNpmPresent]);
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