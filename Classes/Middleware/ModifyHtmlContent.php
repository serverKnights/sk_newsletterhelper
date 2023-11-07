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

        $factory = new Factory();
        $compiler = new Compiler($factory);
        $name ="/var/www/html/" . md5($content);
        $compiler->compile($content, $name);

        $html = file_get_contents($name);
        $body = new Stream('php://temp', 'rw');
        $body->write($html);
        return $response->withBody($body);
    }


}