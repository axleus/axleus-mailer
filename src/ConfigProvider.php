<?php

declare(strict_types=1);

namespace Axleus\Mailer;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
            static::class  => $this->getAdapterConfig(),
        ];
    }

    public function getAdapterConfig(): array
    {
        return [
            Adapter\AdapterInterface::class => [],
        ];
    }

    public function getDependencies(): array
    {
        return [
            'aliases'   => [
                Adapter\AdapterInterface::class => Adapter\PhpMailer::class, // required mapping
                MailerInterface::class          => Mailer::class,
            ],
            'factories' => [
                Adapter\PhpMailer::class           => Adapter\PhpMailerFactory::class,
                Mailer::class                      => MailerFactory::class,
                Middleware\MailerMiddleware::class => Middleware\MailerMiddlewareFactory::class
            ],
        ];
    }

    public function getTemplates(): array
    {
        return [
            'paths' => [
                'mail'    => [__DIR__ . '/../templates/'],
            ],
        ];
    }
}
