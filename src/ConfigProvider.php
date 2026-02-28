<?php

declare(strict_types=1);

/**
 * This file is part of the Axleus Mailer package.
 *
 * Copyright (c) 2025-2026 Joey Smith <jsmith@webinertia.net>
 * and contributors.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axleus\Mailer;

use Webware\CommandBus\CommandBusInterface;
use Webware\CommandBus\ConfigProvider as BusProvider;

use function class_exists;

class ConfigProvider
{
    /** @return array<string, mixed> */
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

    /** @return array<string, mixed> */
    public function getAxleusConfig(): array
    {
        return [
            Adapter\AdapterInterface::class => [
                'host'        => '127.0.0.1',
                'smtp_auth'   => true,
                'smtp_secure' => '',
                'port'        => 25,
                'username'    => '',
                'password'    => '',
                'from'        => 'registration@example.com',
                'charset'     => 'UTF-8',
                'encoding'    => 'base64',
                'timeout'     => 30,
            ],
        ];
    }

    /** @return array<string, mixed> */
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
                Middleware\MailerMiddleware::class => Middleware\MailerMiddlewareFactory::class,
            ],
        ];

        if (class_exists(CommandBusInterface::class)) {
            $deps['factories'][CommandBus\SendEmailCommandHandler::class] = CommandBus\SendEmailCommandHandlerFactory::class;
        }

        return $deps;
    }

    /** @return array<string, mixed> */
    public function getCommandMap(): array
    {
        return [
            CommandBus\SendEmailCommand::class => CommandBus\SendEmailCommandHandler::class,
        ];
    }

    /** @return array<string, mixed> */
    public function getTemplates(): array
    {
        return [
            'paths' => [
                'mail' => [__DIR__ . '/../templates/'],
            ],
        ];
    }
}
