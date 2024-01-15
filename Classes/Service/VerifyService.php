<?php

namespace ServerKnights\SkNewsletterhelper\Service;

use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class VerifyService
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


    public function checkNode(){
        $command = (PHP_OS_FAMILY === 'Windows') ? 'where node' : 'which node';

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

    public function checkMjml(){
        //$command = 'find . -name "mjml*"';
        $command = 'cd /; find . -name "*mjml*"';
        // Execute the command
        $output = shell_exec($command);

        $result = $this->checkMjmlFoldernames($output);

        if($result == false){
            return false;
        }else{
            return $result;
        }
    }


    private function checkMjmlFoldernames($string){
        // Split the string by spaces
        $names = $this->loadMjmlFoldernames();
        // Split the string by spaces
        $paths = explode("\n", $string);
        $pathToNodeModules = "";

        $namesToCheck = [];

        // Extract the name after  "node_modules/" in each path
        foreach ($paths as $path) {
            if (basename(dirname($path)) === 'node_modules') {
                if(empty($pathToNodeModules)){
                    $nodeModulesLength= strlen('node_modules') + strpos($path, 'node_modules/');
                    $pathToNodeModules = substr($path, 0, $nodeModulesLength);
                }
                $namesToCheck[] = basename($path);
            }
        }

        sort($names);
        sort($namesToCheck);

        if($names == $namesToCheck){
            //remove dot if its first char
            if (substr($pathToNodeModules, 0, 1) === '.') {
                $pathToNodeModules = substr($pathToNodeModules, 1);
            }
            return $pathToNodeModules;
        }else{
            return false;
        }
    }

    function loadMjmlFoldernames() {
        // Check if the file exists
        if (!file_exists(__DIR__."/mjmlFolderList.json")) {
            return "File not found: ".__DIR__."/mjmlFolderList.json";
        }
        // Read the file contents
        $jsonContent = file_get_contents(__DIR__."/mjmlFolderList.json");

        // Decode the JSON content
        $names = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return "Error decoding JSON: " . json_last_error_msg();
        }

        return $names;
    }

    private function extractMjmlFoldernames($string){
        // Split the string by spaces
        $paths = explode("\n", $string);

        // Initialize an array to hold the names
        $names = [];

        // Extract the name after  "node_modules/" in each path
        foreach ($paths as $path) {
            if (basename(dirname($path)) === 'node_modules') {
                $names[] = basename($path);
            }

        }
        // Convert the names array to JSON
        $json = json_encode($names, JSON_PRETTY_PRINT);

        // Write the JSON to a file
        file_put_contents('mjmlFolderList.json', $json);

        echo "JSON written to names.json\n";

    }

}