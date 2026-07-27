<?php

declare(strict_types=1);

use Elavora\Api\Extension\LogStdout\Contracts\LogWriter;
use Elavora\Api\Extension\LogStdout\StdoutLogConfig;
use Elavora\Api\Extension\LogStdout\StdoutLogExtension;
use Elavora\Api\Extension\LogStdout\StdoutLogWriter;
use Elavora\Api\Framework\Application;
use Elavora\Api\Framework\Contracts\LogWriter as FrameworkLogWriter;
use PHPUnit\Framework\TestCase;

final class StdoutLogDefaultsTest extends TestCase
{
    public function testConfigUsesStdoutByDefault(): void
    {
        self::assertSame('stdout', StdoutLogConfig::fromArray()->stream());
    }

    public function testConfigRejectsUnsupportedStream(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('O stream de log deve ser stdout ou stderr.');

        StdoutLogConfig::fromArray(['stream' => 'arquivo']);
    }

    public function testExtensionCreatesNativeWriterByDefault(): void
    {
        $application = Application::create()->extend(new StdoutLogExtension());

        self::assertInstanceOf(
            StdoutLogWriter::class,
            $application->container()->get(LogWriter::class)
        );
    }

    public function testExtensionRejectsInvalidLocalWriter(): void
    {
        $application = Application::create()->extend(new StdoutLogExtension());
        $application->container()->bind(LogWriter::class, new stdClass());

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('O container retornou um writer stdout invalido.');

        $application->container()->get(FrameworkLogWriter::class);
    }
}
