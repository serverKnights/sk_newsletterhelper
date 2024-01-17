<?php

namespace ServerKnights\SkNewsletterhelper\Service;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class ExtentionConfigurationService
{
    private ExtensionConfiguration $extensionConfiguration;
    private String $extentionNodePath;
    private String $extentionMjmlPath;
    public function init()
    {
        $this->extensionConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $this->extentionNodePath = $this->extensionConfiguration->get('sk_newsletterhelper', 'NodePath');
        $this->extentionMjmlPath = $this->extensionConfiguration->get('sk_newsletterhelper', 'NodeModulesPath');
    }
    public function reset()
    {
    }
    public function __destruct()
    {
    }


    private function checkNode(){
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
        if(PHP_OS_FAMILY === 'Windows'){
            // TODO test on windows server
            $command = 'powershell "Get-ChildItem -Path C:\\ -Recurse -Filter *mjml* -ErrorAction SilentlyContinue | % { $_.FullName }"';
        }else{
            //$command = 'find . -name "*mjml*"';
            $command = 'ls -R / 2>/dev/null | grep "mjml"';
        }

        // Execute the command
        $output = exec($command);
        $paths = explode((PHP_OS_FAMILY === 'Windows') ? "\r\n" : "\n", $output);
        $pathToNodeModules = [];

        // Extract the name up to "node_modules/" in each path and store unique paths
        foreach ($paths as $path) {
            // Find the position of 'node_modules' in the path
            $nodeModulesPos = strpos($path, 'node_modules');
            if ($nodeModulesPos !== false) {
                // Extract the substring from start to 'node_modules'
                // Store the path in the set (associative array)
                $pathToNodeModules[substr($path, 0, $nodeModulesPos + strlen('node_modules'))] = true;
            }
        }

        // Convert the keys of the associative array to a numeric array
        $pathToNodeModules = array_keys($pathToNodeModules);

        if(empty($pathToNodeModules)){
            return false;
        }else if(count($pathToNodeModules) > 1){
            //TODO make user choose witch path
            return $pathToNodeModules[0];
        }else{
            return $pathToNodeModules[0];
        }
    }


    private function setPath($nodePath,$mjmlPath){
        $this->extensionConfiguration->set('sk_newsletterhelper', ["NodePath" => $nodePath, "NodeModulesPath" => $mjmlPath]);
    }

    public function checkAndSetNode(){
        $assignArray = ['isNodePresent' => false];
        $nodePath = $this->checkNode();

        if($this->isValidPath($nodePath)){
            //set the value to the settings
            $this->setPath($nodePath,$this->extentionMjmlPath);
            $assignArray['isNodePresent'] = true;
            $assignArray['nodePath'] = $nodePath;
        }else{
            //set the settings to null if not installed
            $this->setPath(null,$this->extentionMjmlPath);
        }
        return $assignArray;
    }

    public function checkAndSetMjml(){
        $assignArray = ['isMjmlPresent' => false];
        $mjmlPath = $this->checkMjml();

        if($this->isValidPath($mjmlPath)){
            //set the value to the settings
            $this->setPath($this->extentionNodePath,$mjmlPath);
            $assignArray['isMjmlPresent'] = true;
            $assignArray['mjmlPath'] = $mjmlPath;
        }else{
            //set the settings to null if not installed
            $this->setPath($this->extentionNodePath,null);
        }
        return $assignArray;
    }
    public function installMjml(){
        $targetDirName = 'sk_newsletterhelper';
        // Find the position of the target directory in the path
        $pos = strpos(__DIR__, $targetDirName);
        // Extract the path up to and including the target directory
        $baseDir = substr(__DIR__, 0, $pos + strlen($targetDirName));
        //$command = 'find . -name "mjml*"';
        $command = 'cd '.$baseDir.'; composer install';
        // Execute the command
        exec($command, $output, $returnStatus);

        return $this->checkAndSetMjml();
    }

    public function checkIfExtentionSettingsAreFilled(){
         return !empty($this->extentionNodePath) && !empty($this->extentionMjmlPath);
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



    //Depricated
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

    private function loadMjmlFoldernames() {
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