# Guia de uso

Use `stdout` para coleta normal do runtime ou `stderr` quando a plataforma separar erros.

```php
use Elavora\Api\Extension\LogStdout\StdoutLogExtension;
use Elavora\Api\Framework\Application;
use Elavora\Api\Framework\Logging\Logger;

$application = Application::create()->extend(new StdoutLogExtension([
    'stream' => 'stderr',
]));

$logger = $application->container()->get(Logger::class);
$logger->error('Servico indisponivel', ['service' => 'billing']);
```

O writer preserva o callback injetavel para integracoes customizadas. No writer nativo, a linha JSON termina com `PHP_EOL` e qualquer falha de escrita e propagada.

## Validacao do pacote

Execute a partir da raiz do clone:

```bash
docker run --rm -v "${PWD}:/workspace" -w /workspace composer:2 composer update --no-interaction --no-progress --prefer-dist
docker run --rm -v "${PWD}:/workspace" -w /workspace composer:2 composer check
```
