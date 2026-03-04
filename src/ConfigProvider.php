<?php

declare(strict_types=1);

namespace Axleus\Mailer;

use Webware\CommandBus\ConfigProvider as BusProvider;
use Webware\CommandBus\CommandBusInterface;

use function class_exists;

class ConfigProvider implements ConfigProvider
{
    public function __invoke(): array
    {
        $deps = [
            static::class  => $this->getAxleusConfig(),
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
        ];

        if (class_exists(CommandBusInterface::class)) {
            $deps[CommandBusInterface::class] = [
                BusProvider::COMMAND_MAP_KEY => $this->getCommandMap(),
            ];
        }

        return $deps;
    }

    public function getAxleusConfig(): array
    {
        return [
            Adapter\AdapterInterface::class => [
                'host'      => '127.0.0.1',
                'smtp_auth' => true,
                'port'      => 25,
                'username'  => '',
                'password'  => '',
                'from'      => 'registration@example.com',
            ],
        ];
    }

    public function getDependencies(): array
    {
        $deps = [
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

        if (class_exists(CommandBusInterface::class)) {
            $deps['factories'][CommandBus\SendEmailCommandHandler::class] = CommandBus\SendEmailCommandHandlerFactory::class;
        }

        return $deps;
    }

    public function getCommandMap(): array
    {
        return [
            CommandBus\SendEmailCommand::class => CommandBus\SendEmailCommandHandler::class,
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
