<?php

declare(strict_types=1);

namespace Axleus\Mailer\CommandBus;

use Axleus\Mailer\MailerInterface;
use Override;
use Webware\CommandBus\Command\CommandResultInterface;
use Webware\CommandBus\CommandHandlerInterface;
use Webware\CommandBus\Command\CommandResult;
use Webware\CommandBus\Command\CommandStatus;
use Webware\CommandBus\CommandInterface;

final readonly class SendEmailCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private MailerInterface $mailer
    ) {
    }

    /**
     * 
     * @param CommandInterface&SendEmailCommand $command 
     * @return CommandResultInterface 
     */
    #[Override]
    public function handle(CommandInterface $command): CommandResultInterface
    {
        try {
            $this->mailer->send($command->getBody());
        } catch (\Throwable $e) { // track down the specific exception thrown by the mailer adapter and catch that instead of Throwable
            return new CommandResult(
                $command,
                CommandStatus::Failure,
                $e->getMessage()
            );
        }

        return new CommandResult(
            $command,
            CommandStatus::Success,
            'Email sent successfully'
        );
    }
}
