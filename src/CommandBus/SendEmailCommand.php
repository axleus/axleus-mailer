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

namespace Axleus\Mailer\CommandBus;

use Axleus\Mailer\Event\MessageEvent;
use Override;
use Webware\CommandBus\CommandInterface;
use Webware\CommandBus\Event\EventAwareInterface;
use Webware\CommandBus\Event\EventInterface;

final readonly class SendEmailCommand implements CommandInterface, EventAwareInterface
{
    public function __construct(
        private string $to,
        private string $subject,
        private string $body,
        private MessageEvent $event
    ) {}

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

    #[Override]
    public function getEvent(): MessageEvent
    {
        return $this->event;
    }

    #[Override]
    public function setEvent(EventInterface|MessageEvent $event): void {}
}
