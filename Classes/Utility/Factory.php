<?php

namespace ServerKnights\SkNewsletterhelper\Utility;

use ServerKnights\SkNewsletterhelper\Interface\FactoryInterface;
use ServerKnights\SkNewsletterhelper\Utility\MLFile;

class Factory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createCompiler(): MLFile
    {
        $mjml_path = dirname(__DIR__) . '/../node_modules/.bin/mjml';
        return new MLFile($mjml_path);
    }

    /**
     * @inheritDoc
     */
    public function createNodeExe(): MLFile
    {
        return new MLFile('/usr/bin/node');
    }
}