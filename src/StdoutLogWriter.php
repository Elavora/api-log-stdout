<?php

declare(strict_types=1);

namespace Elavora\Api\Extension\LogStdout;

use Elavora\Api\Extension\LogStdout\Contracts\LogWriter;
use Closure;
use JsonException;
use RuntimeException;

final class StdoutLogWriter implements LogWriter
{
    /** @var Closure(string): void */
    private readonly Closure $writeLine;

    /**
     * @param StdoutLogConfig $config Configuracao do stream.
     * @param callable|null $writeLine Escritor de linha opcional para testes.
     */
    public function __construct(StdoutLogConfig $config, ?callable $writeLine = null)
    {
        $this->writeLine = Closure::fromCallable(
            $writeLine ?? static function (string $line) use ($config): void {
                $target = $config->stream() === 'stderr' ? 'php://stderr' : 'php://stdout';
                $line .= PHP_EOL;
                $writtenBytes = @file_put_contents($target, $line);

                if ($writtenBytes === false || $writtenBytes !== strlen($line)) {
                    throw new RuntimeException(
                        "Nao foi possivel gravar o log em {$config->stream()}."
                    );
                }
            }
        );
    }

    /**
     * @throws JsonException
     */
    public function write(array $entry): void
    {
        ($this->writeLine)(json_encode($entry, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
}
