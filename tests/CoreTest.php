<?php

namespace Kerns\Dummy\Tests;

use Kerns\Dummy\Runtime;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CoreTest extends TestCase
{
    use CreatesAssets;

    protected function setUp(): void
    {
        $this->createAssets();
    }

    protected function tearDown(): void
    {
        $this->removeAssets();
    }

    public function test_original_text_commands_and_templates_work_without_short_tags(): void
    {
        self::assertSame('A useful headline', $this->captureOutput(fn () => dummy('text@headline')));
        self::assertSame('Dynamic 4', $this->captureOutput(fn () => dummy('text@dynamic')));
        self::assertSame('Legacy text', $this->captureOutput(fn () => dummy('text@legacy')));
        self::assertStringContainsString('Dummy:', $this->captureOutput(fn () => dummy('text@empty')));
        self::assertStringContainsString('Dummy:', $this->captureOutput(fn () => dummy('text@missing')));
        self::assertStringContainsString('Dummy:', $this->captureOutput(fn () => dummy('text@../../../LICENSE')));
    }

    public function test_every_bundled_text_pool_can_be_rendered(): void
    {
        $GLOBALS['dummy_assets_path'] = __DIR__ . '/../dummy/assets';
        foreach (glob($GLOBALS['dummy_assets_path'] . '/text/*.txt') as $file) {
            $output = $this->captureOutput(fn () => dummy('text@' . basename($file, '.txt')));
            self::assertNotSame('', $output, $file);
            self::assertStringNotContainsString('<?', $output, $file);
        }
    }

    public function test_probability_boundaries_and_variable_ranges_keep_their_meaning(): void
    {
        for ($i = 0; $i < 50; $i++) {
            self::assertFalse(dumb_luck('0%'));
            self::assertTrue(dumb_luck('100%'));
            $count = 0;
            while (dumb_luck('3-8')) {
                self::assertLessThan(9, ++$count);
            }
            self::assertGreaterThanOrEqual(3, $count);
        }
        self::assertFalse(dumb_luck('0'));
        self::assertFalse(dumb_luck('0-0'));
        $count = 0;
        while (dumb_luck('8-3')) {
            self::assertLessThan(9, ++$count);
        }
        self::assertGreaterThanOrEqual(3, $count);
    }

    public function test_nested_loops_indices_and_position_queries_are_preserved(): void
    {
        $outer = [];
        while (dumb_luck('3')) {
            $outer[] = [
                $this->captureOutput(fn () => dumb_luck('')),
                dumb_question('order:first'),
                dumb_question('order:inner'),
                dumb_question('order:last'),
            ];
            $inner = 0;
            while (dumb_luck('2')) {
                self::assertLessThanOrEqual(2, ++$inner);
            }
            self::assertSame(2, $inner);
        }
        self::assertSame([
            ['1', true, false, false],
            ['2', false, true, false],
            ['3', false, false, true],
        ], $outer);
    }

    public function test_existing_url_and_segment_queries_work_without_apache(): void
    {
        Runtime::$query = ['mode' => 'compact', 'flag' => ''];
        Runtime::$requestPath = '/news/world';
        self::assertTrue(dumb_question('url:mode=compact&flag'));
        self::assertFalse(dumb_question('url:missing'));
        self::assertFalse(dumb_question('url:mode=expanded'));
        self::assertSame('?mode=compact&flag=', dumb_question('url'));
        self::assertTrue(dumb_question('segment:1=news&2=world'));
        self::assertFalse(dumb_question('segment:9=missing'));
        self::assertSame('world', $this->captureOutput(fn () => dumb_question('segment:2')));
        self::assertSame('', $this->captureOutput(fn () => dumb_question('segment:9')));
        self::assertSame('/news/world', dumb_question('segment'));
        Runtime::$query = ['dummyurl' => '/legacy/path'];
        self::assertTrue(dumb_question('segment:1=legacy'));
    }

    public static function imageCommands(): array
    {
        return [
            ['image/one@80x40', 80, 40],
            ['image/one@80x,16:9', 80, 45],
            ['image/one@16:9,x45', 80, 45],
            ['image/one@x45,16:9', 80, 45],
            ['image/one@1:1', 80, 80],
            ['image/portrait@40x', 40, 60],
            ['image/!avatar@20x20', 20, 20],
        ];
    }

    #[DataProvider('imageCommands')]
    public function test_existing_image_syntax_produces_the_requested_dimensions(string $command, int $width, int $height): void
    {
        $file = $this->captureOutput(fn () => dummy($command, true));
        self::assertFileExists($file);
        $size = getimagesize($file);
        self::assertSame([$width, $height], [$size[0], $size[1]]);
    }

    public function test_png_crops_preserve_transparency_and_center_the_source(): void
    {
        $file = $this->captureOutput(fn () => dummy('image/one@40x40', true));
        $image = imagecreatefrompng($file);
        $edge = imagecolorsforindex($image, imagecolorat($image, 20, 0));
        $center = imagecolorsforindex($image, imagecolorat($image, 20, 20));
        self::assertGreaterThan(0, $edge['alpha']);
        self::assertSame(255, $center['green']);
        self::assertSame(0, $center['red']);
        self::assertSame(0, $center['blue']);
        // A center crop removes 20 source pixels from either side. At x=12,
        // stretching the entire 120px-wide source would incorrectly yield red.
        $cropped = imagecolorsforindex($image, imagecolorat($image, 12, 20));
        self::assertSame(255, $cropped['green']);
        self::assertSame(0, $cropped['red']);
    }

    public function test_aspect_only_crops_keep_the_original_source_resolution(): void
    {
        $this->writeImage('image/one/shared.png', 2100, 100, 'png');
        $file = $this->captureOutput(fn () => dummy('image/one@21:1', true));
        $size = getimagesize($file);
        self::assertSame([2100, 100], [$size[0], $size[1]]);
    }

    public function test_image_caches_are_reused_and_do_not_collide_across_folders(): void
    {
        $first = $this->captureOutput(fn () => dummy('image/one@60x40', true));
        touch($first, 1000000000);
        clearstatcache();
        self::assertSame($first, $this->captureOutput(fn () => dummy('image/one@60x40', true)));
        self::assertSame(1000000000, filemtime($first));
        $other = $this->captureOutput(fn () => dummy('image/two@60x40', true));
        self::assertNotSame($first, $other);
        touch($this->directory . '/assets/image/one/shared.png', 1000000000);
        clearstatcache();
        self::assertNotSame($first, $this->captureOutput(fn () => dummy('image/one@60x40', true)));
    }

    public function test_images_do_not_repeat_until_the_pool_is_exhausted(): void
    {
        $first = $this->captureOutput(fn () => dummy('image/landscape'));
        $second = $this->captureOutput(fn () => dummy('image/landscape'));
        self::assertNotSame($first, $second);
        self::assertContains($this->captureOutput(fn () => dummy('image/landscape')), [$first, $second]);
        self::assertStringContainsString('/assets/image/', $first);
        foreach (getFilesRecursively($this->directory . '/assets/image') as $file) {
            self::assertStringNotContainsString('!avatar', $file);
        }
    }

    public function test_missing_and_outside_asset_paths_fail_without_php_warnings(): void
    {
        self::assertSame([], getFilesRecursively($this->directory . '/assets/image', '../../'));
        self::assertStringContainsString('Dummy:', $this->captureOutput(fn () => dummy('image/missing@40x40')));
        self::assertStringContainsString('Dummy:', $this->captureOutput(fn () => dummy('image/../../../@40x40')));
        self::assertStringContainsString('Dummy:', $this->captureOutput(fn () => dummy('ad@missing')));
        self::assertStringContainsString('Dummy:', $this->captureOutput(fn () => dummy('ad@../text')));
        self::assertStringContainsString('dimensions', $this->captureOutput(fn () => dummy('image/one@1x,1:999999')));
    }

    public function test_ad_markup_and_per_folder_overrides_keep_working(): void
    {
        $markup = $this->captureOutput(fn () => dummy('ad@120x90'));
        self::assertStringContainsString('/dummy/assets/ad/120x90/example.png', $markup);
        self::assertStringContainsString('width="120" height="90"', $markup);
        file_put_contents($this->directory . '/assets/ad/120x90/image.embed.php', 'Custom <?= $width ?> <?= $dummy_path ?>');
        self::assertSame('Custom 120 /dummy', $this->captureOutput(fn () => dummy('ad@120x90')));
    }

    public function test_composer_bootstrap_has_no_output_session_or_memory_limit_side_effects(): void
    {
        $autoload = var_export(realpath(__DIR__ . '/../vendor/autoload.php'), true);
        $script = '$memory = ini_get("memory_limit"); ob_start(); require ' . $autoload . '; '
            . '$output = ob_get_clean(); echo json_encode([$output, session_status(), $memory === ini_get("memory_limit"), function_exists("dummy")]);';
        $process = proc_open([PHP_BINARY, '-r', $script], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        $result = stream_get_contents($pipes[1]);
        $errors = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        self::assertSame(0, proc_close($process), $errors);
        self::assertSame(['', PHP_SESSION_NONE, true, true], json_decode($result, true));
    }
}
