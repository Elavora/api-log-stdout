# api-log-stdout

[![Packagist Version](https://img.shields.io/packagist/v/elavora/api-log-stdout.svg?style=flat-square)](https://packagist.org/packages/elavora/api-log-stdout)
[![PHP Version](https://img.shields.io/packagist/php-v/elavora/api-log-stdout.svg?style=flat-square)](https://packagist.org/packages/elavora/api-log-stdout)
[![Composer Quality](https://github.com/Elavora/api-log-stdout/actions/workflows/quality.yml/badge.svg?branch=main)](https://github.com/Elavora/api-log-stdout/actions/workflows/quality.yml)
[![CodeQL](https://github.com/Elavora/api-log-stdout/actions/workflows/codeql.yml/badge.svg?branch=main)](https://github.com/Elavora/api-log-stdout/actions/workflows/codeql.yml)
[![License](https://img.shields.io/packagist/l/elavora/api-log-stdout.svg?style=flat-square)](https://packagist.org/packages/elavora/api-log-stdout)

Writer opcional para enviar logs JSON a `stdout` ou `stderr`.

## Requisitos

- PHP 8.3 ou superior.
- `elavora/api-framework` 1.x.

## Instalacao

```bash
composer require elavora/api-log-stdout
```

## Inicio rapido

```php
use Elavora\Api\Extension\LogStdout\StdoutLogExtension;
use Elavora\Api\Framework\Application;
use Elavora\Api\Framework\Logging\Logger;

$application = Application::create()->extend(new StdoutLogExtension([
    'stream' => 'stdout',
]));

$application->container()
    ->get(Logger::class)
    ->info('Requisicao recebida', ['route' => '/health']);
```

Cada entrada ocupa uma linha. Uma falha ou escrita parcial no descritor gera `RuntimeException`.

## Documentacao

Consulte o [guia de uso](docs/USO.md) para configurar o stream e validar o pacote.
