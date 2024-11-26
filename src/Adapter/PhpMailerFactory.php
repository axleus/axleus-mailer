<?php

declare(strict_types=1);

namespace Axleus\Mailer\Adapter;

use Axleus\Mailer\ConfigProvider;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use PHPMailer\PHPMailer\PHPMailer as BaseMailer;
use Psr\Container\ContainerInterface;

final class PhpMailerFactory
{
    public function __invoke(ContainerInterface $container): PhpMailer
    {
        $config = $container->get('config')[ConfigProvider::class];
        if (empty($config[AdapterInterface::class])) {
            throw new ServiceNotCreatedException(
                'Service: ' . PhpMailer::class . ' could not be created. Missing configuration.'
            );
        }
        $config = $config[AdapterInterface::class];
        $mailer = new BaseMailer(true); // enable exceptions
        //$mailer->isSMTP();
        $mailer->Host     = $config['host'];
        //$mailer->SMTPAuth = $config['smtp_auth'];
        $mailer->Port     = $config['port'];
        $mailer->Username = $config['username'];
        $mailer->Password = $config['password'];
        return new PhpMailer($mailer);
    }
}
