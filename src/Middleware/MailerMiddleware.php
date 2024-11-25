<?php

declare(strict_types=1);

namespace Axleus\Mailer\Middleware;

use Axleus\Mailer\MailerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MailerMiddleware implements MiddlewareInterface
{
    public final const TEMPLATE_KEY     = 'message_templates';
    public final const FROM_ADDRESS_KEY = 'from';

    public function __construct(
        private MailerInterface $mailer,
        private array $config
    ){
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $adapter = $this->mailer->getAdapter();
        $adapter->from($this->config[static::FROM_ADDRESS_KEY]);
        return $handler->handle($request->withAttribute(MailerInterface::class, $this->mailer));
    }
}
