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

interface MailerInterface
{
    public function setAdapter(Adapter\AdapterInterface $adapter): self;

    public function getAdapter(): ?Adapter\AdapterInterface;

    public function send(): bool;
}
