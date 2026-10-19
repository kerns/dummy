<?php

namespace Kerns\Dummy\Tests;

use PHPUnit\Framework\TestCase;

final class StandaloneTest extends TestCase
{
    private function runPhp(string $script): array
    {
        $process = proc_open([PHP_BINARY, '-d', 'error_reporting=-1', '-d', 'short_open_tag=0', '-r', $script],
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        $output = stream_get_contents($pipes[1]);
        $errors = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        return [proc_close($process), $output, $errors];
    }

    public function test_the_direct_image_endpoint_still_returns_image_bytes(): void
    {
        $source = var_export(realpath(__DIR__ . '/../dummy/dummy.php'), true);
        [$exit, $output, $errors] = $this->runPhp('$_SERVER["SCRIPT_FILENAME"] = ' . $source . '; '
            . '$_SERVER["QUERY_STRING"] = "image@64x32"; require ' . $source . ';');
        self::assertSame(0, $exit, $errors);
        self::assertSame('', $errors);
        $size = getimagesizefromstring($output);
        self::assertSame([64, 32], [$size[0], $size[1]]);
    }

    public function test_an_entry_point_also_named_dummy_php_is_not_intercepted(): void
    {
        $directory = sys_get_temp_dir() . '/dummy-entry-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $entry = $directory . '/dummy.php';
        $source = var_export(realpath(__DIR__ . '/../dummy/dummy.php'), true);
        file_put_contents($entry, '<?php require ' . $source . '; echo "Application entry point";');
        try {
            [$exit, $output, $errors] = $this->runPhp('$_SERVER["SCRIPT_FILENAME"] = ' . var_export($entry, true)
                . '; require ' . var_export($entry, true) . ';');
            self::assertSame(0, $exit, $errors);
            self::assertSame('Application entry point', $output);
            self::assertSame('', $errors);
        } finally {
            unlink($entry);
            rmdir($directory);
        }
    }

    public function test_the_demo_renders_without_short_tags_or_php_errors(): void
    {
        $source = var_export(realpath(__DIR__ . '/../demo.php'), true);
        [$exit, $output, $errors] = $this->runPhp('require ' . $source . ';');
        self::assertSame(0, $exit, $errors);
        self::assertSame('', $errors);
        self::assertStringContainsString('<h1>Welcome to Dummy', $output);
        self::assertStringNotContainsString('<?', $output);
        self::assertStringNotContainsString('<em>Dummy:', $output);
    }

    public function test_demo_photo_has_enough_pixels_for_a_high_density_display(): void
    {
        $source = var_export(realpath(__DIR__ . '/../demo.php'), true);
        [$exit, $output, $errors] = $this->runPhp('require ' . $source . ';');
        self::assertSame(0, $exit, $errors);
        self::assertSame('', $errors);
        preg_match('~<img src="([^"]+)"[^>]*width="(\d+)"~', $output, $matches);
        self::assertNotEmpty($matches);
        $image = getimagesize(__DIR__ . '/..' . $matches[1]);
        self::assertGreaterThanOrEqual((int) $matches[2] * 3, $image[0]);
    }

    public function test_copying_only_the_dummy_folder_still_works_without_composer(): void
    {
        $directory = sys_get_temp_dir() . '/dummy-drop-in-' . bin2hex(random_bytes(8));
        mkdir($directory . '/dummy/assets/text', 0775, true);
        copy(__DIR__ . '/../dummy/dummy.php', $directory . '/dummy/dummy.php');
        copy(__DIR__ . '/../dummy/Runtime.php', $directory . '/dummy/Runtime.php');
        copy(__DIR__ . '/../dummy/Lorem.php', $directory . '/dummy/Lorem.php');
        file_put_contents($directory . '/dummy/assets/text/headline.txt', 'Drop-in installation');
        try {
            [$exit, $output, $errors] = $this->runPhp('require ' . var_export($directory . '/dummy/dummy.php', true)
                . '; dummy("text@headline"); echo "|"; dummy("text@words:12");');
            self::assertSame(0, $exit, $errors);
            [$headline, $lorem] = explode('|', $output, 2);
            self::assertSame('Drop-in installation', $headline);
            self::assertCount(12, explode(' ', $lorem));
            self::assertSame('', $errors);
        } finally {
            unlink($directory . '/dummy/assets/text/headline.txt');
            unlink($directory . '/dummy/dummy.php');
            unlink($directory . '/dummy/Runtime.php');
            unlink($directory . '/dummy/Lorem.php');
            rmdir($directory . '/dummy/assets/text');
            rmdir($directory . '/dummy/assets');
            rmdir($directory . '/dummy');
            rmdir($directory);
        }
    }
}
