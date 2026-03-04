<?php

declare(strict_types=1);

namespace Axleus\Mailer\CommandBus;

use Axleus\Mailer\MailerInterface;
use Psr\Container\ContainerInterface;

final class SendEmailCommandHandlerFactory
{
    public function __invoke(ContainerInterface $container): SendEmailCommandHandler
    {
        return new SendEmailCommandHandler(
            $container->get(MailerInterface::class)
        );
    }
}
