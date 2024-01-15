<?php

namespace ServerKnights\SkNewsletterhelper\Utility;

use ServerKnights\SkNewsletterhelper\Interface\FactoryInterface;
use ServerKnights\SkNewsletterhelper\Utility\MLFile;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class Factory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createCompiler(): MLFile
    {
        $node_modules = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('sk_newsletterhelper', 'NodeModulesPath');

        // Remove trailing slash if it exists
        $node_modules = rtrim($node_modules, '/');
        $mjml_path =  $node_modules.'/.bin/mjml';
        return new MLFile($mjml_path);
    }

    /**
     * @inheritDoc
     */
    public function createNodeExe(): MLFile
    {
        return new MLFile(\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('sk_newsletterhelper', 'NodePath'));
    }
}