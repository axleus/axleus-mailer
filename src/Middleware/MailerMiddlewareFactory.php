<?php

declare(strict_types=1);

namespace Axleus\Mailer\Middleware;

use Axleus\Mailer\Adapter\AdapterInterface;
use Axleus\Mailer\ConfigProvider;
use Axleus\Mailer\MailerInterface;
use Psr\Container\ContainerInterface;

class MailerMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): MailerMiddleware
    {
        $config       = $container->get('config');
        $mailSettings = $config[ConfigProvider::class][AdapterInterface::class];
        return new MailerMiddleware(
            $container->get(MailerInterface::class),
            $mailSettings
        );
    }
}
