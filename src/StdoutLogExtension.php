<?php

declare(strict_types=1);

namespace Elavora\Api\Extension\LogStdout;

use Elavora\Api\Extension\LogStdout\Contracts\LogWriter;
use Elavora\Api\Framework\Application;
use Elavora\Api\Framework\Contracts\Extension;
use Elavora\Api\Framework\Contracts\LogWriter as FrameworkLogWriter;
use Elavora\Api\Framework\Logging\Logger;
use Closure;

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
            fn (): FrameworkLogWriter => $application->container()->get(LogWriter::class)
        );
        $application->container()->bind(
            Logger::class,
            fn (): Logger => new Logger($application->container()->get(LogWriter::class))
        );
    }

    private function createWriter(): LogWriter
    {
        if ($this->writerFactory !== null) {
            return ($this->writerFactory)($this->config);
        }

        return new StdoutLogWriter($this->config);
    }
}
