<?php

namespace Kerns\Dummy\Tests;

use Kerns\Dummy\Runtime;

trait CreatesAssets
{
    protected string $directory;
    private array $previousGlobals;

    protected function createAssets(): void
    {
        $this->directory = sys_get_temp_dir() . '/dummy-tests-' . bin2hex(random_bytes(8));
        $this->previousGlobals = [];
        foreach (['dummy_path', 'dummy_assets_path', 'dummy_cache_path', 'flash_ads', 'delimiter'] as $name) {
            $this->previousGlobals[$name] = $GLOBALS[$name];
        }
        foreach (['text', 'image/landscape', 'image/portrait', 'image/!avatar', 'image/one', 'image/two', 'ad/120x90'] as $path) {
            mkdir($this->directory . '/assets/' . $path, 0775, true);
        }
        file_put_contents($this->directory . '/assets/text/headline.txt', "# Comment\n\nA useful headline\n");
        file_put_contents($this->directory . '/assets/text/dynamic.txt', '<?php echo "Dynamic ", 2 + 2; ?>');
        file_put_contents($this->directory . '/assets/text/legacy.txt', '<? echo "Legacy text"; ?>');
        file_put_contents($this->directory . '/assets/text/empty.txt', "# No values\n\n");

        $this->writeImage('image/landscape/first.png', 120, 80, 'png');
        $this->writeImage('image/landscape/second.png', 120, 80, 'png');
        $this->writeImage('image/portrait/first.jpg', 80, 120, 'jpg');
        $this->writeImage('image/!avatar/face.gif', 40, 40, 'gif');
        $this->writeImage('image/one/shared.png', 120, 80, 'png');
        $this->writeImage('image/two/shared.png', 120, 80, 'png');
        $this->writeImage('ad/120x90/example.png', 120, 90, 'png');
        copy(__DIR__ . '/../dummy/assets/ad/image.embed.php', $this->directory . '/assets/ad/image.embed.php');
        copy(__DIR__ . '/../dummy/assets/ad/flash.embed.php', $this->directory . '/assets/ad/flash.embed.php');

        $GLOBALS['dummy_path'] = '/dummy';
        $GLOBALS['dummy_assets_path'] = $this->directory . '/assets';
        $GLOBALS['dummy_cache_path'] = $this->directory . '/cache';
        $GLOBALS['flash_ads'] = false;
        Runtime::reset();
    }

    protected function writeImage(string $path, int $width, int $height, string $format): void
    {
        $image = imagecreatetruecolor($width, $height);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        $clear = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefill($image, 0, 0, $clear);
        $red = imagecolorallocate($image, 255, 0, 0);
        $green = imagecolorallocate($image, 0, 255, 0);
        $blue = imagecolorallocate($image, 0, 0, 255);
        imagefilledrectangle($image, 0, 1, (int) ($width / 3), $height - 2, $red);
        imagefilledrectangle($image, (int) ($width / 3), 1, (int) (2 * $width / 3), $height - 2, $green);
        imagefilledrectangle($image, (int) (2 * $width / 3), 1, $width - 1, $height - 2, $blue);
        if ($format === 'gif') {
            imagecolortransparent($image, $clear);
        }
        $file = $this->directory . '/assets/' . $path;
        match ($format) {
            'png' => imagepng($image, $file),
            'jpg' => imagejpeg($image, $file),
            'gif' => imagegif($image, $file),
        };
    }

    protected function captureOutput(callable $render): string
    {
        ob_start();
        try {
            $render();
            return ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }

    protected function removeAssets(): void
    {
        Runtime::reset();
        foreach ($this->previousGlobals as $name => $value) {
            $GLOBALS[$name] = $value;
        }
        $remove = static function (string $path) use (&$remove): void {
            foreach (new \FilesystemIterator($path) as $entry) {
                if ($entry->isDir() && !$entry->isLink()) {
                    $remove($entry->getPathname());
                } else {
                    unlink($entry->getPathname());
                }
            }
            rmdir($path);
        };
        $remove($this->directory);
    }
}
