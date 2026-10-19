<?php

namespace Kerns\Dummy\Tests;

use DOMDocument;
use DOMXPath;
use PHPUnit\Framework\TestCase;

final class StaticDemoTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir() . '/dummy-static-test-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
    }

    protected function tearDown(): void
    {
        $remove = static function (string $path) use (&$remove): void {
            if (is_dir($path) && !is_link($path)) {
                foreach (new \FilesystemIterator($path) as $entry) {
                    $remove($entry->getPathname());
                }
                rmdir($path);
            } else {
                unlink($path);
            }
        };
        $remove($this->directory);
    }

    private function build(string $destination): array
    {
        $process = proc_open([PHP_BINARY, '-d', 'error_reporting=-1', '-d', 'short_open_tag=0',
            __DIR__ . '/../tools/build-demo.php', $destination], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        $output = stream_get_contents($pipes[1]);
        $errors = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        return [proc_close($process), $output, $errors];
    }

    public function test_export_contains_all_local_resources_and_works_below_a_url_prefix(): void
    {
        $destination = $this->directory . '/dummy';
        [$exit, $output, $errors] = $this->build($destination);
        self::assertSame(0, $exit, $errors . $output);
        self::assertSame('', $errors);
        $html = file_get_contents($destination . '/index.html');
        self::assertStringContainsString('generated snapshot', $html);
        self::assertStringNotContainsString('<?', $html);
        self::assertStringNotContainsString('<em>Dummy:', $html);
        self::assertFileExists($destination . '/.nojekyll');
        self::assertFileExists($destination . '/CREDITS.md');
        self::assertFileExists($destination . '/LICENSE');
        self::assertFileExists($destination . '/demo_files/vendor/highlight/LICENSE');
        $document = new DOMDocument();
        $previousErrors = libxml_use_internal_errors(true);
        $document->loadHTML($html);
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrors);
        foreach ((new DOMXPath($document))->query('//*[@src or @href]') as $node) {
            foreach (['src', 'href'] as $attribute) {
                $url = $node->getAttribute($attribute);
                self::assertDoesNotMatchRegularExpression('~^/dummy/~', $url);
                if (preg_match('~^(dummy/|demo_files/)~', $url)) {
                    self::assertFileExists($destination . '/' . rawurldecode($url), $url);
                }
            }
        }
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($destination)) as $file) {
            self::assertNotSame('php', $file->getExtension());
        }
        // Rebuilding removes the previous generated files without retaining stale crops.
        file_put_contents($destination . '/stale.txt', 'Old export');
        [$exit, , $errors] = $this->build($destination);
        self::assertSame(0, $exit, $errors);
        self::assertFileDoesNotExist($destination . '/stale.txt');
    }

    public function test_export_refuses_to_replace_an_unrelated_directory(): void
    {
        file_put_contents($this->directory . '/keep.txt', 'Existing content');
        [$exit, , $errors] = $this->build($this->directory);
        self::assertSame(1, $exit);
        self::assertStringContainsString('must be empty', $errors);
        self::assertSame('Existing content', file_get_contents($this->directory . '/keep.txt'));
        self::assertFileDoesNotExist($this->directory . '/index.html');
    }
}
