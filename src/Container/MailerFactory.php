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

namespace Axleus\Mailer\Container;

use Axleus\Mailer\Adapter\AdapterInterface;
use Axleus\Mailer\Mailer;
use Axleus\Mailer\MailerInterface;
use Psr\Container\ContainerInterface;

final class MailerFactory
{
    public function __invoke(ContainerInterface $container): MailerInterface
    {
        /** @var AdapterInterface $adapter */
        $adapter = $container->get(AdapterInterface::class);

        return new Mailer($adapter);
    }
}
