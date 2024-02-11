<?php
declare(strict_types=1);

namespace ServerKnights\SkNewsletterhelper\Middleware;

use Cassandra\Uuid;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ServerKnights\SkNewsletterhelper\Utility\Compiler;
use ServerKnights\SkNewsletterhelper\Utility\Factory;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\Http\Stream;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use ServerKnights\SkNewsletterhelper\Service\ExtentionConfigurationService;

class SkModifyHtmlContent implements MiddlewareInterface
{
    private $debug = false;
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if ($response instanceof NullResponse) {
            return $response;
        }
        // extract the content
        $body = $response->getBody();
        $body->rewind();
        $content = $response->getBody()->getContents();


        //DebuggerUtility::var_dump($response->getBody()->getContents());
        //die();

        $startTag = '<mjml>';
        $endTag = '</mjml>';
        $extention = GeneralUtility::makeInstance(ExtentionConfigurationService::class);
        $extention->init();
        $startPos = strpos($content, $startTag);
        if ($startPos !== false && $extention->checkIfExtentionSettingsAreFilled()) {
            $endPos = strpos($content, $endTag, $startPos);
            if ($endPos !== false) {

                $scriptRegexPattern = '/<script[^>]*?(?:\/>|>[^<]*?<\/script>)/im';
                $mjmlTextRegexPattern = '/<mj-text[^>]*?>([\s\S]*?)<\/mj-text>/im';
                $configuratorBaseRegexPattern = '/<input type="hidden" id="sk_newsletterhelper_configurator_base64" value=".*?\/>/s';
                preg_match($configuratorBaseRegexPattern, $content, $configuratorMatch);
                preg_match_all($scriptRegexPattern, $content, $scriptMatches);
                preg_match_all($mjmlTextRegexPattern, $content, $mjmlMatches);

                // Add the length of the end tag to include it in the final substring
                $endPos += strlen($endTag);
                // Extract the substring between the start and end tags
                $content = substr($content, $startPos, $endPos - $startPos);

                if((!empty($scriptMatches[0]) && !empty($request->getQueryParams()["isBackend"])) || $this->debug){
                    if (!empty($mjmlMatches[0])) {

                        $count = 1;//use the count to identify the right text
                        foreach ($mjmlMatches[0] as $match) {
                            $content = str_replace($match, $this->addHtmlAttribute_in_HTML_Tag($match,"mj-text","css-class","sk-text sk-text-".$count), $content);
                            $count++;
                        }
                    }
                }
                $factory = new Factory();
                $compiler = new Compiler($factory);
                $name ="/var/www/html/" . md5($content);
                $compiler->compile($content, $name);

                $html = file_get_contents($name);

                //if backendContext add the script tags to the head
                if((!empty($scriptMatches[0]) && !empty($request->getQueryParams()["isBackend"])) || $this->debug){
                    foreach ($scriptMatches[0] as $match) {
                        $html = str_replace("</head>", $match."</head>", $html);
                    }
                    if (!empty($configuratorMatch[0])) {
                        $html = str_replace("</body>", $configuratorMatch[0]."</body>", $html);
                    }
                    $html = str_replace("</html>", "<div style='display:none;' id='sk-mjml-template'>".$content."</div></html>", $html);
                }
            } else {
                $html = $content;
            }
        } else {
            $html = $content;
        }


        $body = new Stream('php://temp', 'rw');
        $body->write($html);
        return $response->withBody($body);
    }
    public function addHtmlAttribute_in_HTML_Tag($htmlStr, $tagname, $attributeName, $attributeValue): string
    {
        /** if html tag attribute does not exist then add it ... */
        if (!preg_match("~<$tagname\s.*?$attributeName=([\'\"])~i", $htmlStr)) {
            $htmlStr = preg_replace('/(<' . $tagname . '\b[^><]*)>/i', '$1 ' . $attributeName . '="' . $attributeValue . '">', $htmlStr, 1);
        } else {
            // If the attribute already exists, replace its value
            $htmlStr = preg_replace("~(<$tagname\s.*?$attributeName=)([\'\"])(.*?)([\'\"])~i", '$1$2' . $attributeValue . '$4', $htmlStr,1);
        }
        return $htmlStr;
    }



}