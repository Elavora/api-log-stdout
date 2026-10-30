# elavora/api-log-stdout

[![Packagist Version](https://img.shields.io/packagist/v/elavora/api-log-stdout.svg?style=flat-square)](https://packagist.org/packages/elavora/api-log-stdout)
[![PHP Version](https://img.shields.io/packagist/php-v/elavora/api-log-stdout.svg?style=flat-square)](https://packagist.org/packages/elavora/api-log-stdout)
[![Qualidade](https://github.com/Elavora/api-log-stdout/actions/workflows/quality.yml/badge.svg?branch=main)](https://github.com/Elavora/api-log-stdout/actions/workflows/quality.yml)
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

Especificação completa, exemplos e validação local: [docs/USO.md](docs/USO.md).

## Instalação

```bash
composer require elavora/api-log-stdout
```

Requisitos: PHP 8.1 ou superior e `elavora/api-framework` `^0.3.1`.

## Desenvolvimento e testes

Você só precisa de Docker. O ambiente é a imagem `ghcr.io/elavora/php-dev:8.3-latest`: abra o repositório no VS Code
com **Dev Containers: Reopen in Container**, ou rode no PowerShell:

```powershell
docker run --rm --mount "type=bind,source=$($PWD.Path),target=/workspace" -w /workspace ghcr.io/elavora/php-dev:8.3-latest sh -lc 'git config --global --add safe.directory /workspace && composer install --no-interaction --prefer-dist && composer check'
```

Scripts do Composer:

```bash
composer check     # lint, testes e PHPStan
composer test      # testes PHPUnit
composer lint      # sintaxe (elavora-standards lint)
composer analyse   # PHPStan, nível 8
```

O código segue o padrão PER-CS, com as regras do pacote `elavora/code-standards`. No devcontainer, o arquivo é
formatado ao salvar; no PR, o CI formata o que faltou e commita a correção (`style: organiza o código`).
Os testes rodam no CI, no workflow **Qualidade**, em todo PR. O passo a passo, o que roda no CI e como reproduzi-lo
estão em [Desenvolvimento e testes](docs/DESENVOLVIMENTO.md).

## Como contribuir

1. Crie uma branch a partir da `main` (`feat/...`, `fix/...` ou `docs/...`).
2. Faça commits em português do Brasil, no padrão Conventional Commits (`feat: ...`, `fix: ...`, `docs: ...`).
3. Rode `composer check` antes de abrir o PR.
4. Abra o PR para a `main`, preencha o template e aplique as labels que definem a versão
   ([Versões e release](docs/AUTOMACAO.md#versões-e-release)).
5. Confira o comentário do pipeline de qualidade no PR. Ele não bloqueia o merge, mas quem revisa olha o resultado.

## Versões

Cada PR integrado na `main` gera uma versão automaticamente, conforme as labels do PR: sem a label `release` sai uma
beta (`0.3.0-beta.1`), com ela sai a estável (`0.3.0`). As tags novas não têm o prefixo `v`, e as notas de cada
GitHub Release são geradas em português a partir dos PRs. Veja [Versões e release](docs/AUTOMACAO.md#versões-e-release).

## Documentação

1. [Guia de uso](docs/USO.md): a especificação do pacote, com a API pública e exemplos.
2. [Desenvolvimento e testes](docs/DESENVOLVIMENTO.md): ambiente, como rodar os testes e o que roda no CI.
3. [Automação do repositório](docs/AUTOMACAO.md): workflows, Dependabot, estilo de código, labels e versões.

## Licença

MIT
