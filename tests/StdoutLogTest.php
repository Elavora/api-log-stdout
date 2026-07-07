<?php

declare(strict_types=1);

use Elavora\Api\Extension\LogStdout\Contracts\LogWriter;
use Elavora\Api\Extension\LogStdout\StdoutLogConfig;
use Elavora\Api\Extension\LogStdout\StdoutLogExtension;
use Elavora\Api\Extension\LogStdout\StdoutLogWriter;
use Elavora\Api\Framework\Application;
use Elavora\Api\Framework\Contracts\LogWriter as FrameworkLogWriter;
use Elavora\Api\Framework\Logging\Logger;
use PHPUnit\Framework\TestCase;

final class StdoutLogTest extends TestCase
{
    public function testWritesJsonLineToConfiguredCallback(): void
    {
        $lines = [];
        $writer = new StdoutLogWriter(
            StdoutLogConfig::fromArray(['stream' => 'stderr']),
            static function (string $line) use (&$lines): void {
                $lines[] = $line;
            }
        );

        $writer->write(['message' => 'teste', 'level' => 'info']);

        self::assertCount(1, $lines);
        self::assertJsonStringEqualsJsonString('{"message":"teste","level":"info"}', $lines[0]);
    }

    public function testExtensionRegistersLocalAndFrameworkContracts(): void
    {
        $fakeWriter = new class implements LogWriter {
            public function write(array $entry): void
            {
            }
        };
        $application = Application::create()->extend(new StdoutLogExtension(
            ['stream' => 'stdout'],
            static fn (StdoutLogConfig $config): LogWriter => $fakeWriter
        ));

        self::assertSame($fakeWriter, $application->container()->get(LogWriter::class));
        self::assertSame($fakeWriter, $application->container()->get(FrameworkLogWriter::class));
        self::assertInstanceOf(Logger::class, $application->container()->get(Logger::class));
    }

    public function testReusesSameWriterInstanceForLocalAndFrameworkContracts(): void
    {
        $fakeWriter = new class implements LogWriter {
            public function write(array $entry): void
            {
            }
        };
        $application = Application::create()->extend(new StdoutLogExtension(
            ['stream' => 'stdout'],
            static fn (StdoutLogConfig $config): LogWriter => $fakeWriter
        ));

        self::assertSame(
            $application->container()->get(LogWriter::class),
            $application->container()->get(FrameworkLogWriter::class)
        );
    }
}
