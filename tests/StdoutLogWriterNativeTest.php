<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class StdoutLogWriterNativeTest extends TestCase
{
    public function testNativeWriterKeepsJsonLineInStdoutAndStderr(): void
    {
        foreach (['stdout', 'stderr'] as $stream) {
            $result = $this->runWriter($stream, false);

            self::assertSame(0, $result['exitCode'], $stream);
            self::assertSame(
                '{"message":"teste"}' . PHP_EOL,
                $result[$stream],
                $stream
            );
        }
    }

    public function testNativeWriterThrowsWhenDescriptorIsClosed(): void
    {
        foreach (['stdout', 'stderr'] as $stream) {
            $result = $this->runWriter($stream, true);

            self::assertSame(0, $result['exitCode'], $stream);
        }
    }

    /**
     * @return array{exitCode: int, stdout: string, stderr: string}
     */
    private function runWriter(string $stream, bool $closeDescriptor): array
    {
        $scriptPath = $this->temporaryFile('api-log-stdout-script-');
        $stdoutPath = $this->temporaryFile('api-log-stdout-output-');
        $stderrPath = $this->temporaryFile('api-log-stderr-output-');
        $autoloadPath = dirname(__DIR__) . '/vendor/autoload.php';

        $script = <<<'PHP'
<?php

declare(strict_types=1);

require $argv[1];

use Elavora\Api\Extension\LogStdout\StdoutLogConfig;
use Elavora\Api\Extension\LogStdout\StdoutLogWriter;

$stream = $argv[2];
$closeDescriptor = $argv[3] === '1';

if ($closeDescriptor) {
    fclose($stream === 'stdout' ? STDOUT : STDERR);
}

try {
    $writer = new StdoutLogWriter(StdoutLogConfig::fromArray(['stream' => $stream]));
    $writer->write(['message' => 'teste']);

    exit($closeDescriptor ? 2 : 0);
} catch (RuntimeException $exception) {
    exit($closeDescriptor && str_contains($exception->getMessage(), $stream) ? 0 : 3);
}
PHP;

        if (file_put_contents($scriptPath, $script) === false) {
            self::fail('Nao foi possivel criar o subprocesso de teste.');
        }

        $nullDevice = PHP_OS_FAMILY === 'Windows' ? 'NUL' : '/dev/null';
        $pipes = [];
        $process = proc_open(
            [PHP_BINARY, $scriptPath, $autoloadPath, $stream, $closeDescriptor ? '1' : '0'],
            [
                0 => ['file', $nullDevice, 'r'],
                1 => ['file', $stdoutPath, 'w'],
                2 => ['file', $stderrPath, 'w'],
            ],
            $pipes
        );

        if (!is_resource($process)) {
            self::fail('Nao foi possivel iniciar o subprocesso de teste.');
        }

        try {
            $exitCode = proc_close($process);
            $process = null;

            return [
                'exitCode' => $exitCode,
                'stdout' => (string) file_get_contents($stdoutPath),
                'stderr' => (string) file_get_contents($stderrPath),
            ];
        } finally {
            if (is_resource($process)) {
                proc_terminate($process);
                proc_close($process);
            }

            @unlink($scriptPath);
            @unlink($stdoutPath);
            @unlink($stderrPath);
        }
    }

    private function temporaryFile(string $prefix): string
    {
        $path = tempnam(sys_get_temp_dir(), $prefix);
        if ($path === false) {
            self::fail('Nao foi possivel criar um arquivo temporario de teste.');
        }

        return $path;
    }
}
