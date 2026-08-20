<?php
declare(strict_types=1);

namespace Pop\Console\Test;

use Pop\Console\Console;
use Pop\Console\Prompt;
use PHPUnit\Framework\TestCase;

class PromptTest extends TestCase
{

    private function createInputStream(string ...$lines): mixed
    {
        $stream = fopen('php://memory', 'r+');
        foreach ($lines as $line) {
            fwrite($stream, $line . PHP_EOL);
        }
        rewind($stream);
        return $stream;
    }

    public function testPrompt()
    {
        $prompt = new Prompt('    ', null, $this->createInputStream('y'));

        ob_start();
        $answer = $prompt->prompt('Test prompt: ');
        ob_get_clean();

        $this->assertEquals('y', $answer);
    }

    public function testPromptWithFormattedHeader()
    {
        $prompt = new Prompt('    ', 'Test Header:' . PHP_EOL, $this->createInputStream('y'));

        ob_start();
        $prompt->prompt('Test prompt: ');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, 'Test Header:'));
    }

    public function testMultiLinePromptSequenceFromSingleStream()
    {
        $prompt = new Prompt('    ', null, $this->createInputStream('x', 'y'));

        ob_start();
        $first  = $prompt->prompt('Test prompt: ');
        $second = $prompt->prompt('Test prompt: ');
        ob_get_clean();

        $this->assertEquals('x', $first);
        $this->assertEquals('y', $second);
    }

    public function testPromptWithOptions()
    {
        $prompt = new Prompt('    ', null, $this->createInputStream('n'));

        ob_start();
        $answer = $prompt->prompt('Test prompt: ', ['Y', 'N']);
        $result = ob_get_clean();

        $this->assertEquals('n', $answer);
        $this->assertTrue(str_contains($result, '    Test prompt: '));
    }

    public function testPromptRetriesOnInvalidOption()
    {
        $prompt = new Prompt('    ', null, $this->createInputStream('x', 'y'));

        ob_start();
        $answer = $prompt->prompt('Test prompt: ', ['Y', 'N']);
        $result = ob_get_clean();

        $this->assertEquals('y', $answer);
        $this->assertEquals(2, substr_count($result, 'Test prompt: '));
    }

    public function testPromptWithOptionsAtEndOfStreamReturnsEmptyStringInsteadOfLooping()
    {
        $prompt = new Prompt('    ', null, $this->createInputStream());

        ob_start();
        $answer = $prompt->prompt('Test prompt: ', ['Y', 'N']);
        ob_get_clean();

        $this->assertEquals('', $answer);
    }

    public function testPromptWithIndent()
    {
        $prompt = new Prompt('    ', null, $this->createInputStream('y'));

        ob_start();
        $prompt->prompt('Test prompt: ');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, '    Test prompt: '));
    }

    public function testPromptMultiSingleSelection()
    {
        $prompt = new Prompt('    ', null, $this->createInputStream('2'));

        ob_start();
        $result = $prompt->promptMulti('Select: ', ['1', '2', '3']);
        ob_get_clean();

        $this->assertEquals(['2'], $result);
    }

    public function testPromptMultiMultipleSelections()
    {
        $prompt = new Prompt('    ', null, $this->createInputStream('1,3'));

        ob_start();
        $result = $prompt->promptMulti('Select: ', ['1', '2', '3']);
        ob_get_clean();

        $this->assertEquals(['1', '3'], $result);
    }

    public function testPromptMultiTrimsWhitespaceAroundTokens()
    {
        $prompt = new Prompt('    ', null, $this->createInputStream(' 1 ,  3 '));

        ob_start();
        $result = $prompt->promptMulti('Select: ', ['1', '2', '3']);
        ob_get_clean();

        $this->assertEquals(['1', '3'], $result);
    }

    public function testPromptMultiDeduplicatesRepeatedTokens()
    {
        $prompt = new Prompt('    ', null, $this->createInputStream('2,2,1'));

        ob_start();
        $result = $prompt->promptMulti('Select: ', ['1', '2', '3']);
        ob_get_clean();

        $this->assertEquals(['2', '1'], $result);
    }

    public function testPromptMultiRetriesOnInvalidToken()
    {
        $prompt = new Prompt('    ', null, $this->createInputStream('1,9', '2'));

        ob_start();
        $result = $prompt->promptMulti('Select: ', ['1', '2', '3']);
        ob_get_clean();

        $this->assertEquals(['2'], $result);
    }

    public function testPromptMultiEmptyInputReturnsEmptyArray()
    {
        $prompt = new Prompt('    ', null, $this->createInputStream(''));

        ob_start();
        $result = $prompt->promptMulti('Select: ', ['1', '2', '3']);
        ob_get_clean();

        $this->assertEquals([], $result);
    }

    public function testPromptMultiAtEndOfStreamReturnsEmptyArray()
    {
        $prompt = new Prompt('    ', null, $this->createInputStream());

        ob_start();
        $result = $prompt->promptMulti('Select: ', ['1', '2', '3']);
        ob_get_clean();

        $this->assertEquals([], $result);
    }

    public function testPromptMultiIsCaseInsensitiveByDefault()
    {
        $prompt = new Prompt('    ', null, $this->createInputStream('WEB,CLI'));

        ob_start();
        $result = $prompt->promptMulti('Select: ', ['web', 'cli']);
        ob_get_clean();

        $this->assertEquals(['web', 'cli'], $result);
    }

    public function testPromptMultiCaseSensitiveRejectsWrongCase()
    {
        $prompt = new Prompt('    ', null, $this->createInputStream('WEB', 'web'));

        ob_start();
        $result = $prompt->promptMulti('Select: ', ['web', 'cli'], true);
        ob_get_clean();

        $this->assertEquals(['web'], $result);
    }

    public function testConfirmYes()
    {
        $prompt = new Prompt('    ', null, $this->createInputStream('y'));

        ob_start();
        $answer = $prompt->confirm();
        ob_get_clean();

        $this->assertEquals('y', $answer);
    }

    public function testConfirmNoWithExitFalseReturnsResponseWithoutExiting()
    {
        $prompt = new Prompt('    ', null, $this->createInputStream('n'));

        ob_start();
        $answer = $prompt->confirm(exit: false);
        ob_get_clean();

        $this->assertEquals('n', $answer);
    }

    public function testConfirmAtEndOfStreamReturnsEmptyStringInsteadOfLooping()
    {
        $prompt = new Prompt('    ', null, $this->createInputStream());

        ob_start();
        $answer = $prompt->confirm(exit: false);
        ob_get_clean();

        $this->assertEquals('', $answer);
    }

    // Facade tests - Console delegates to Prompt

    public function testConsolePromptDelegatesToPrompt()
    {
        $console = new Console();
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->setInputStream($this->createInputStream('y'));

        ob_start();
        $answer = $console->prompt('Test prompt: ');
        ob_get_clean();

        $this->assertEquals('y', $answer);
    }

    public function testConsolePromptMultiDelegatesToPrompt()
    {
        $console = new Console();
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->setInputStream($this->createInputStream('2'));

        ob_start();
        $result = $console->promptMulti('Select: ', ['1', '2', '3']);
        ob_get_clean();

        $this->assertEquals(['2'], $result);
    }

    public function testConsoleConfirmDelegatesToPrompt()
    {
        $console = new Console();
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->setInputStream($this->createInputStream('y'));

        ob_start();
        $answer = $console->confirm();
        ob_get_clean();

        $this->assertEquals('y', $answer);
    }

    // Subprocess tests - test integration with real stdin/process exit

    protected function runInSubprocess(string $code, string $stdin): array
    {
        $autoload       = dirname(__DIR__) . '/vendor/autoload.php';
        $descriptorSpec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open([PHP_BINARY, '-r', $code, $autoload], $descriptorSpec, $pipes);

        fwrite($pipes[0], $stdin);
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        return [$stdout, $stderr, $exitCode];
    }

    public function testPromptReadsFromRealStdin()
    {
        $code = 'require $argv[1]; $c = new \Pop\Console\Console(); fwrite(STDOUT, $c->prompt("Prompt: "));';
        [$stdout, $stderr, $exitCode] = $this->runInSubprocess($code, "hello\n");

        $this->assertStringEndsWith('hello', $stdout, $stderr);
        $this->assertEquals(0, $exitCode, $stderr);
    }

    public function testConfirmNoExitsWithCode127()
    {
        $code = 'require $argv[1]; $c = new \Pop\Console\Console(); $c->confirm();';
        [$stdout, $stderr, $exitCode] = $this->runInSubprocess($code, "n\n");

        $this->assertEquals(127, $exitCode, $stderr);
    }

}
