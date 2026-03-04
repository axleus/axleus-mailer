# axleus/axleus-mailer

A PHP email abstraction library for [Laminas Mezzio](https://docs.mezzio.dev/) applications. It decouples application code from any specific mail transport library through an adapter pattern, allowing the underlying mailer to be swapped via configuration without touching application code.

> **Note:** This library is currently undergoing a refactor. Full documentation will follow once the refactor is complete.

## Features

- Adapter-based architecture — ship with a `PHPMailer` adapter; add others without changing application code
- PSR-15 middleware that pre-configures the mailer and injects it as a request attribute
- Laminas delegator (`MailerAwareDelegator`) for zero-config injection into any `MailerAwareInterface` service
- Optional command bus integration via `webware/command-bus`
- Optional event support via `webware/commandbus-event`
- Full Laminas Service Manager wiring through a `ConfigProvider`

## Requirements

- PHP 8.2 or later
- Laminas Mezzio application

## Installation

```bash
composer require axleus/axleus-mailer
```

If your application uses `laminas/laminas-component-installer`, the `ConfigProvider` will be registered automatically. Otherwise, add it manually to your configuration aggregator:

```php
new Axleus\Mailer\ConfigProvider(),
```

## Basic Configuration

Override the default adapter settings in your application config:

```php
use Axleus\Mailer\ConfigProvider;
use Axleus\Mailer\Adapter\AdapterInterface;

return [
    ConfigProvider::class => [
        AdapterInterface::class => [
            'host'      => 'smtp.example.com',
            'smtp_auth' => true,
            'port'      => 587,
            'username'  => 'user@example.com',
            'password'  => 'secret',
            'from'      => 'no-reply@example.com',
        ],
    ],
];
```

## Basic Usage

### Via PSR-15 Middleware

Add `MailerMiddleware` to your pipeline. Downstream handlers can then retrieve the mailer from the request:

```php
use Axleus\Mailer\MailerInterface;

$mailer = $request->getAttribute(MailerInterface::class);
```

### Via MailerAwareInterface

Implement `MailerAwareInterface` on any service and add the `MailerAwareInterfaceTrait`. Register `MailerAwareDelegator` as a delegator for that service in your container config — the mailer will be injected automatically at resolution time.

### Via Command Bus (optional)

Inject `CommandBusInterface` and call `handle()` with a `SendEmailCommand`:

```php
use Axleus\Mailer\CommandBus\SendEmailCommand;
use Webware\CommandBus\CommandBusInterface;

class MyRequestHandler
{
    public function __construct(
        private CommandBusInterface $commandBus
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $result = $this->commandBus->handle(new SendEmailCommand(
            to: 'recipient@example.com',
            subject: 'Hello',
            body: '<p>Hello, world!</p>',
        ));

        // $result is a CommandResultInterface (Success or Failure)
    }
}
```

The `SendEmailCommandHandler` is mapped automatically via `ConfigProvider` and executed through the command bus middleware pipeline.

## License

BSD-3-Clause. See [LICENSE](LICENSE) for details.
