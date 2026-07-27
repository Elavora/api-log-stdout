<?php

declare(strict_types=1);

namespace Elavora\Api\Extension\LogStdout;

use Elavora\Api\Extension\LogStdout\Contracts\LogWriter;
use Elavora\Api\Framework\Application;
use Elavora\Api\Framework\Contracts\Extension;
use Elavora\Api\Framework\Contracts\LogWriter as FrameworkLogWriter;
use Elavora\Api\Framework\Logging\Logger;
use Closure;
use LogicException;

final class StdoutLogExtension implements Extension
{
    private readonly StdoutLogConfig $config;

    /** @var Closure(StdoutLogConfig): LogWriter|null */
    private readonly ?Closure $writerFactory;

    /**
     * @param array{stream?: string} $config Configuracao do stream stdout/stderr.
     * @param callable|null $writerFactory Factory opcional para testes ou integracao customizada.
     */
    public function __construct(array $config = [], ?callable $writerFactory = null)
    {
        $this->config = StdoutLogConfig::fromArray($config);
        $this->writerFactory = $writerFactory === null
            ? null
            : Closure::fromCallable($writerFactory);
    }

    /**
     * Registra o writer stdout/stderr e o logger estruturado.
     */
    public function register(Application $application): void
    {
        $application->container()->bind(
            LogWriter::class,
            fn (): LogWriter => $this->createWriter()
        );
        $application->container()->bind(
            FrameworkLogWriter::class,
            fn (): FrameworkLogWriter => $this->writer($application)
        );
        $application->container()->bind(
            Logger::class,
            fn (): Logger => new Logger($this->writer($application))
        );
    }

    private function writer(Application $application): LogWriter
    {
        $writer = $application->container()->get(LogWriter::class);
        if (!$writer instanceof LogWriter) {
            throw new LogicException('O container retornou um writer stdout invalido.');
        }

        return $writer;
    }

    private function createWriter(): LogWriter
    {
        if ($this->writerFactory !== null) {
            return ($this->writerFactory)($this->config);
        }

        return new StdoutLogWriter($this->config);
    }
}
