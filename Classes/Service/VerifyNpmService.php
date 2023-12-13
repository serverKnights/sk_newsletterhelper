<?php

namespace ServerKnights\SkNewsletterhelper\Service;

class VerifyNpmService
{

    public function init()
    {
    }
    public function reset()
    {
    }
    public function __destruct()
    {
    }


    public function checkNpm(){
        $command = (PHP_OS_FAMILY === 'Windows') ? 'where npm' : 'which npm';

        // Execute the command
        $output = shell_exec($command);

        // Check if the output contains the path to npm
        if ($output !== null && trim($output) !== '') {
            // npm found, return the path
            return trim($output);
        } else {
            // npm not found
            return false;
        }
    }

}