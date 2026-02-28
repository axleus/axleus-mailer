<?php

declare(strict_types=1);

namespace Axleus\Mailer\CommandBus;

use Webware\CommandBus\CommandInterface;

final readonly class SendEmailCommand implements CommandInterface
{
    public function __construct(
        private string $to,
        private string $subject,
        private string $body
    ) {
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}
