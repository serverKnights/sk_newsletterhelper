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
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;


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

        $startPos = strpos($content, $startTag);
        if ($startPos !== false) {
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