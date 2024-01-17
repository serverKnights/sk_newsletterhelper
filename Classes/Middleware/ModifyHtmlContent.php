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

class ModifyHtmlContent implements MiddlewareInterface
{

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

        $startTag = '<mjml>';
        $endTag = '</mjml>';
        $extention = GeneralUtility::makeInstance(ExtentionConfigurationService::class);
        $extention->init();
        $startPos = strpos($content, $startTag);
        if ($startPos !== false && $extention->checkIfExtentionSettingsAreFilled()) {
            $endPos = strpos($content, $endTag, $startPos);
            if ($endPos !== false) {
                // Add the length of the end tag to include it in the final substring
                $endPos += strlen($endTag);
                // Extract the substring between the start and end tags
                $content = substr($content, $startPos, $endPos - $startPos);

                $factory = new Factory();
                $compiler = new Compiler($factory);
                $name ="/var/www/html/" . md5($content);

                $compiler->compile($content, $name);

                $html = file_get_contents($name);
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


}