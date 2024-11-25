<?php

declare(strict_types=1);

namespace Axleus\Mailer\Middleware;

use Axleus\Mailer\MailerInterface;
use Psr\Container\ContainerInterface;

class MailerMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): MailerMiddleware
    {
        return new MailerMiddleware(
            $container->get(MailerInterface::class),
            $container->get('config')
        );
    }
}