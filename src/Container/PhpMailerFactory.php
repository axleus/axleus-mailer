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
use Axleus\Mailer\Adapter\MessageInterface;
use Axleus\Mailer\Adapter\PhpMailer;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use PHPMailer\PHPMailer\PHPMailer as BaseMailer;
use Psr\Container\ContainerInterface;

final class PhpMailerFactory
{
    public function __invoke(ContainerInterface $container): AdapterInterface&PhpMailer
    {
        /** @var array<string, mixed> $appConfig */
        $appConfig = $container->get('config');

        /** @var array<string, mixed> $adapterConfig */
        $adapterConfig = $appConfig[MessageInterface::class];
        if (empty($adapterConfig[AdapterInterface::class])) {
            throw new ServiceNotCreatedException('Service: ' . PhpMailer::class . ' could not be created. Missing configuration.');
        }

        /** @var array<string, mixed> $config */
        $config = $adapterConfig[AdapterInterface::class];
        $mailer = new BaseMailer($config['enableExceptions'] ?? true); // enable exceptions

        if (isset($config['useSmtp']) && (bool) $config['useSmtp'] === true) {
            $this->configureSmtp($mailer, $config);
        }
        
        return new PhpMailer($mailer);
    }

    private function configureSmtp(BaseMailer $mailer, array $messageConfig): void
    {
        $mailer->isSMTP();
        $mailer->Host     = (string) $messageConfig['host'];
        $mailer->Port     = (int) $messageConfig['port'];
        $mailer->SMTPAuth = (bool) $messageConfig['smtp_auth'];
        $mailer->Username = (string) $messageConfig['username'];
        $mailer->Password = (string) $messageConfig['password'];
        $mailer->CharSet  = (string) ($messageConfig['charset'] ?? 'UTF-8');
        $mailer->Encoding = (string) ($messageConfig['encoding'] ?? 'base64');
        $mailer->Timeout  = (int) ($messageConfig['timeout'] ?? 30);

        if (! empty($messageConfig['smtp_secure'])) {
            $mailer->SMTPSecure = (string) $messageConfig['smtp_secure'];
        }

        if (! empty($messageConfig['from'])) {
            $mailer->setFrom((string) $messageConfig['from']);
        }
    }
}
