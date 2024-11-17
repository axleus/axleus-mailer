<?php

declare(strict_types=1);

namespace Axleus\Mailer;

interface MailerInterface
{
    public function setAdapter(Adapter\AdapterInterface $adapter): self;
    public function getAdapter(): ?Adapter\AdapterInterface;
    public function send(Adapter\AdapterInterface $adapter);
}
