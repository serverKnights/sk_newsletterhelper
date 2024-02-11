<?php

namespace ServerKnights\SkNewsletterhelper\Service;

use TYPO3\CMS\Core\Core\Environment;

class TemplateService
{

    public $tenplatePath = "";

    public function __construct()
    {
        $this->tenplatePath = Environment::getPublicPath()."/fileadmin/sk-templates";
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
}