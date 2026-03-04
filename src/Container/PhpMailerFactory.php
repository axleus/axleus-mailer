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
use Axleus\Mailer\Adapter\PhpMailer;
use Axleus\Mailer\MailerInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use PHPMailer\PHPMailer\PHPMailer as BaseMailer;
use Psr\Container\ContainerInterface;

final class PhpMailerFactory
{
    public function __invoke(ContainerInterface $container): AdapterInterface&PhpMailer
    {
        /** @var array<string, mixed> $appConfig */
        $appConfig = $container->get('config');

        /** @var array<string, mixed> $providerConfig */
        $providerConfig = $appConfig[MailerInterface::class];
        if (empty($providerConfig[AdapterInterface::class])) {
            throw new ServiceNotCreatedException('Service: ' . PhpMailer::class . ' could not be created. Missing configuration.');
        }

        /** @var array<string, mixed> $config */
        $config = $providerConfig[AdapterInterface::class];
        $mailer = new BaseMailer($config['enableExceptions'] ?? true); // enable exceptions

        if (isset($config['useSmtp']) && (bool) $config['useSmtp'] === true) {
            $this->configureSmtp($mailer, $config);
        }
        
        return new PhpMailer($mailer);
    }

    private function configureSmtp(BaseMailer $mailer, array $config): void
    {
        $mailer->isSMTP();
        $mailer->Host     = (string) $config['host'];
        $mailer->Port     = (int) $config['port'];
        $mailer->SMTPAuth = (bool) $config['smtp_auth'];
        $mailer->Username = (string) $config['username'];
        $mailer->Password = (string) $config['password'];
        $mailer->CharSet  = (string) ($config['charset'] ?? 'UTF-8');
        $mailer->Encoding = (string) ($config['encoding'] ?? 'base64');
        $mailer->Timeout  = (int) ($config['timeout'] ?? 30);

        if (! empty($config['smtp_secure'])) {
            $mailer->SMTPSecure = (string) $config['smtp_secure'];
        }

        if (! empty($config['from'])) {
            $mailer->setFrom((string) $config['from']);
        }
    }
}
