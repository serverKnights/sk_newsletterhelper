<?php

namespace ServerKnights\SkNewsletterhelper\Interface;

use ServerKnights\SkNewsletterhelper\Utility\MLFile;

interface FactoryInterface
{
    /**
     * Returns a \Mjml\File object that represents the mjml executable
     *
     * @return MLFile
     */
    public function createCompiler(): MLFile;

    /**
     * Returns a \Mjml\File instance that represents the node executable
     *
     * @return MLFile
     */
    public function createNodeExe(): MLFile;
}