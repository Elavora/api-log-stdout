# elavora/api-log-stdout

[![Packagist Version](https://img.shields.io/packagist/v/elavora/api-log-stdout.svg?style=flat-square)](https://packagist.org/packages/elavora/api-log-stdout)
[![PHP Version](https://img.shields.io/packagist/php-v/elavora/api-log-stdout.svg?style=flat-square)](https://packagist.org/packages/elavora/api-log-stdout)
[![Composer Quality](https://github.com/Elavora/api-log-stdout/actions/workflows/quality.yml/badge.svg?branch=main)](https://github.com/Elavora/api-log-stdout/actions/workflows/quality.yml)
[![CodeQL](https://github.com/Elavora/api-log-stdout/actions/workflows/codeql.yml/badge.svg?branch=main)](https://github.com/Elavora/api-log-stdout/actions/workflows/codeql.yml)
[![License](https://img.shields.io/packagist/l/elavora/api-log-stdout.svg?style=flat-square)](LICENSE)
Pacote opcional de logs em `stdout` ou `stderr` para o framework Elavora.
E indicado para Docker, Kubernetes e plataformas que coletam logs do processo.

```php
use Elavora\Api\Extension\LogStdout\StdoutLogExtension;
use Elavora\Api\Framework\Logging\Logger;

$application->extend(new StdoutLogExtension([
    'stream' => 'stdout',
]));

$application->container()
    ->get(Logger::class)
    ->info('Requisicao recebida', ['route' => '/health']);
```

Cada entrada e gravada como uma linha JSON com `timestamp`, `level`,
`message`, `request_id` e `context`.
