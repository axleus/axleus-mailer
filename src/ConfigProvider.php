<?php

declare(strict_types=1);

namespace Axleus\Mailer;

use Axleus\Core\ConfigProviderInterface;

class ConfigProvider implements ConfigProviderInterface
{
    public function __invoke(): array
    {
        return [
            static::AXLEUS_KEY => [static::class => $this->getAxleusSettings()],
            'dependencies'     => $this->getDependencies(),
            'templates'        => $this->getTemplates(),
        ];
    }

    public function getAxleusSettings(): array
    {
        return [
            Adapter\AdapterInterface::class => [
                'host'      => '127.0.0.1',
                'smtp_auth' => true,
                'port'      => 25,
                'username'  => '',
                'password'  => '',
                'from' => 'registration@example.com',
            ],
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
