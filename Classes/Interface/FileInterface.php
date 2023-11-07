<?php

namespace ServerKnights\SkNewsletterhelper\Interface;

interface FileInterface
{
    /**
     * Tells if the file is executable
     *
     * @return bool true if executable, false otherwise.
     */
    public function isExecutable();

    /**
     * Returns the path to the file as a string
     *
     * @return string
     */
    public function __toString();
}