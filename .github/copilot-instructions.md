# GitHub Copilot Instructions — axleus/axleus-mailer

## Project Purpose

`axleus/axleus-mailer` is a **PHP email abstraction library** for **Laminas Mezzio** applications. It decouples application code from any specific mailer implementation through an adapter pattern, currently shipping a `PHPMailer` adapter. New adapters can be added without changing application-level code.

## Architecture Overview

```
MailerInterface / Mailer          — Core mailer; holds an adapter and delegates send() to it
Adapter\MessageInterface          — Fluent builder contract for message composition (to/from/subject/body/attach/etc.)
Adapter\AdapterInterface          — Extends MessageInterface; adds transport config (isSmtp/isMail) and send(): bool
Adapter\PhpMailer                 — Adapter wrapping phpmailer/phpmailer; implements AdapterInterface
Middleware\MailerMiddleware        — PSR-15 middleware; injects Mailer as a request attribute
MailerAwareInterface / Trait      — Interface + trait for services that need the mailer injected
MailerAwareDelegator              — Laminas delegator that auto-injects MailerInterface into MailerAwareInterface services
CommandBus\SendEmailCommand       — Immutable command object (to / subject / body)
CommandBus\SendEmailCommandHandler— Handles the command; configures the adapter then calls send()
Event\MessageEvent                — Event wrapping an email message (uses webware/commandbus-event) — in progress
ConfigProvider                    — Laminas/Mezzio config provider wiring all services
```

## Key Design Patterns

- **Interface split**: `MessageInterface` owns all message-building methods (to/from/cc/bcc/replyTo/subject/body/altBody/attach/headers/charset/encoding/reset). `AdapterInterface` extends `MessageInterface` and adds transport config (`isSmtp()`/`isMail()`) and `send(): bool`. Every adapter must satisfy both contracts.
- **Fluent builder**: All `MessageInterface` and `AdapterInterface` methods return `self`, enabling chained calls: `$adapter->to(...)->subject(...)->body(...)->send()`.
- **Adapter pattern**: `MailerInterface::send()` delegates to the internally held `Adapter\AdapterInterface`. All concrete mailer libraries are wrapped in an adapter.
- **PSR-11 Factories**: Every service has a matching `*Factory` class that reads from the container. Factories are registered in `ConfigProvider::getDependencies()`.
- **Laminas Service Manager aliases**: `AdapterInterface::class` is aliased to `PhpMailer::class` (and `MailerInterface::class` to `Mailer::class`), making the concrete implementation swappable via configuration.
- **Delegator pattern**: `MailerAwareDelegator` detects whether a resolved service implements `MailerAwareInterface` and injects the mailer automatically — no manual wiring is required.
- **Optional integrations**: Command bus and event support are conditional on `class_exists(CommandBusInterface::class)`. Do not make them required dependencies.
- **PSR-15 Middleware**: `MailerMiddleware` stores the configured mailer on the request under the key `MailerInterface::class` so downstream handlers can retrieve it via `$request->getAttribute(MailerInterface::class)`.

## Configuration

Top-level config key is `[MailerInterface::class][AdapterInterface::class]`. Adapter settings live under:

```php
$config[MailerInterface::class][AdapterInterface::class] = [
    'host'        => '127.0.0.1',
    'smtp_auth'   => true,
    'smtp_secure' => '',       // 'tls' or 'ssl'
    'port'        => 25,
    'username'    => '',
    'password'    => '',
    'from'        => 'registration@example.com',
    'charset'     => 'UTF-8',
    'encoding'    => 'base64',
    'timeout'     => 30,
];
```

All adapter config lives under the single `AdapterInterface::class` key. To swap the active adapter, change the alias in `ConfigProvider::getDependencies()` — do not add a new config key.

## Coding Conventions

- Always add `declare(strict_types=1);` at the top of every PHP file.
- Use `final` on concrete classes whenever they are not designed to be extended.
- Use `readonly` on classes/properties that hold immutable data (e.g., command objects, factories).
- Use **constructor property promotion** for injected dependencies.
- Use `#[Override]` attribute on methods that override or implement an interface method.
- Namespace root: `Axleus\Mailer\`, test namespace: `AxleusTest\Mailer\`.
- Config keys use `ClassName::class` strings rather than plain strings.
- Detect optional package availability with `class_exists()` before registering related services.

## Adding a New Adapter

1. Create `src/Adapter/YourAdapter.php` implementing `Adapter\AdapterInterface` (which extends `MessageInterface`). All builder methods must return `self`; `send()` must return `bool`.
2. Add `#[Override]` to every method that implements the interface.
3. Create `src/Adapter/YourAdapterFactory.php` — read config from `$container->get('config')[ConfigProvider::class][AdapterInterface::class]`.
4. Register in `ConfigProvider::getDependencies()`:
   - Add a factory entry for `YourAdapter::class`.
   - Update the alias `AdapterInterface::class => YourAdapter::class`.

## Dependencies

| Package | Role |
|---|---|
| `phpmailer/phpmailer` | Concrete mail transport (required) |
| `psr/container` | Container abstraction |
| `psr/http-server-middleware` | PSR-15 middleware interface |
| `webware/command-bus` | Optional command bus integration |
| `webware/commandbus-event` | Optional event support |
| `laminas/laminas-servicemanager` | DI container used at runtime |

Dev tools: `phpunit/phpunit`, `phpstan/phpstan`, `friendsofphp/php-cs-fixer` (via webware/coding-standard).

## Testing

```
composer test               # unit tests
composer test-integration   # integration tests
composer sa                 # PHPStan static analysis
composer cs-check           # coding standard check
composer cs-fix             # auto-fix coding standard
```

PHPUnit config: `phpunit.xml.dist`. PHPStan config: `phpstan.neon.dist` (baseline: `phpstan-baseline.neon`).

## Namespace & Autoloading (PSR-4)

| Namespace | Path |
|---|---|
| `Axleus\Mailer\` | `src/` |
| `AxleusTest\Mailer\` | `test/` |
