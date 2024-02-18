<?php

namespace ServerKnights\SkNewsletterhelper\Service;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;

class TemplateService
{

    public $tenplatePath = "";
    public $customExtensionName = "";
    public $customTemplatesPath = "";

    public function __construct()
    {
        $extensionConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $this->tenplatePath = Environment::getPublicPath()."/fileadmin/sk-templates";
        $this->customExtensionName = $extensionConfiguration->get('sk_newsletterhelper', 'ExtensionName');
        $this->customTemplatesPath = $extensionConfiguration->get('sk_newsletterhelper', 'TemplatePath');
    }

    function createTemplate($pageId,$templateID)
    {
        if(!file_exists( $this->tenplatePath )){
            mkdir( $this->tenplatePath , 0776, true);
        }
        if(!file_exists(  $this->tenplatePath."/".$pageId)){
            mkdir(  $this->tenplatePath."/".$pageId, 0776, true);
        }
        $templateContent = file_get_contents($templateID);
        file_put_contents($this->tenplatePath."/".$pageId."/template.html",$templateContent);
    }

    function hasTemplate($pageId)
    {
        if(!file_exists(  $this->tenplatePath."/".$pageId."/template.html")){
           return false;
        }
        return true;
    }



    function saveTemplate($pageId,$content)
    {
        echo $this->tenplatePath."/".$pageId."/template.html";
        return file_put_contents($this->tenplatePath."/".$pageId."/template.html",$content);
    }

    function removeTemplate($pageId)
    {
        $filePath = $this->tenplatePath . "/" . $pageId . "/template.html";

        // Check if the file exists before attempting to delete it
        if (file_exists($filePath)) {
            unlink($filePath); // Delete the file
            echo "Template removed: " . $filePath."</br></br> Please reload the Page";
            return true;
        } else {
            echo "File not found: " . $filePath;
            return false;
        }
    }

    function isCustomTemplateSet()
    {
        return $this->customExtensionName != "" && $this->customTemplatesPath != "";
    }

    public function getCustomTemplatePath(): mixed
    {
        return $this->customTemplatesPath;
    }

    public function getCustomExtensionName(): mixed
    {
        return $this->customExtensionName;
    }


}