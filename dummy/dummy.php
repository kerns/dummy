<?php

// Dummy — For Rapid Prototyping and QA
// https://github.com/kerns/dummy
// Creative Commons Attribution-Share Alike 3.0 United States.

use Kerns\Dummy\Lorem;
use Kerns\Dummy\Runtime;

require_once __DIR__ . '/Runtime.php';
require_once __DIR__ . '/Lorem.php';

// Existing configuration names remain available before or after loading Dummy.
// Explicit globals also work when Composer loads this file inside its autoloader.
$GLOBALS['dummy_path'] ??= '/dummy';
$GLOBALS['dummy_assets_path'] ??= __DIR__ . '/assets';
$GLOBALS['dummy_cache_path'] ??= __DIR__ . '/cache';
$GLOBALS['flash_ads'] ??= false;
$GLOBALS['delimiter'] ??= '@';

foreach ([
    'JPEG_QUALITY' => 90,
    'MAX_WIDTH_PX' => 2048,
    'MAX_HEIGHT_PX' => 1536,
    'ADVANCED_RANDOM_IMAGES' => true,
    'IGNORE_CACHE' => false,
    'HIDDEN_FOLDER_PREPEND' => '!',
] as $dummyConstant => $dummyDefault) {
    if (!defined($dummyConstant)) {
        define($dummyConstant, $dummyDefault);
    }
}
unset($dummyConstant, $dummyDefault);

function dummy_filter_strings($a)
{
    $a = trim($a);
    return $a !== '' && $a[0] !== '#';
}

function dummy_text($inpath, $filename)
{
    if ($inpath === 'text' && preg_match('/\A(words|sentences|paragraphs):([^,]*)(?:,(.*))?\z/s', (string) $filename, $matches)) {
        $content = Lorem::generate($matches[1], $matches[2], $matches[3] ?? '');
        if ($content === null) {
            Runtime::error('Use a whole-number count or range, such as text@words:50 or text@words:30-60. '
                . 'An optional ,title capitalizes each word. Limits: 10000 words, 1000 sentences, or 100 paragraphs.');
            return;
        }
        echo $content;
        return;
    }
    $root = $GLOBALS['dummy_assets_path'];
    $key = $root . '/' . $inpath . '@' . $filename;
    if (!isset(Runtime::$textPools[$key])) {
        $strings = [];
        if ($filename !== '' && $filename !== null) {
            $file = Runtime::resolve($root, $inpath . '/' . $filename . '.txt');
            if ($file !== null && is_file($file) && is_readable($file)) {
                $strings = file($file, FILE_IGNORE_NEW_LINES);
            }
        } else {
            $basePath = Runtime::resolve($root, $inpath);
            if ($basePath !== null) {
                foreach (getFilesRecursively($basePath) as $file) {
                    if (is_readable($basePath . '/' . $file)) {
                        $strings = array_merge($strings, file($basePath . '/' . $file, FILE_IGNORE_NEW_LINES));
                    }
                }
            }
        }
        Runtime::$textPools[$key] = array_values(array_filter($strings, 'dummy_filter_strings'));
    }
    $strings = Runtime::$textPools[$key];
    if ($strings === []) {
        Runtime::error("Couldn't locate usable text in assets/$inpath@$filename.");
        return;
    }

    // Asset files are trusted local templates, as in the original Dummy.
    // Support their legacy short tags independently of short_open_tag.
    $template = preg_replace('/<\?(?!php(?:\s|$)|=|xml\b)/i', '<?php ', $strings[array_rand($strings)]);
    ob_start();
    try {
        eval('?>' . $template);
        $content = ob_get_contents();
    } finally {
        ob_end_clean();
    }
    echo trim($content);
}

function dummy_ad($folder)
{
    global $dummy_path, $flash_ads;
    $root = $GLOBALS['dummy_assets_path'] . '/ad';
    $path = $root; // Available to existing custom ad templates.
    $folder = $folder ?: '*';
    if (str_contains($folder, "\0") || preg_match('~(^|[/\\\\])\.\.([/\\\\]|$)~', $folder)) {
        Runtime::error('Ad folders must stay inside the assets/ad directory.');
        return;
    }
    $extensions = $GLOBALS['flash_ads'] ? '{jpg,jpeg,gif,png,swf}' : '{jpg,jpeg,gif,png}';
    $ads = array_values(array_filter(
        glob("$root/$folder/*.$extensions", GLOB_BRACE) ?: [],
        static fn ($file) => Runtime::resolve($root, substr($file, strlen($root) + 1)) !== null,
    ));
    if ($ads === []) {
        Runtime::error("Couldn't locate an ad in assets/ad/$folder.");
        return;
    }
    $ad = $ads[array_rand($ads)];
    $overridepath = dirname($ad);
    $inc = strtolower(pathinfo($ad, PATHINFO_EXTENSION)) === 'swf' ? 'flash.embed.php' : 'image.embed.php';
    $useinc = is_file("$overridepath/$inc") ? "$overridepath/$inc" : "$root/$inc";
    $size = basename($overridepath);
    [$width, $height] = array_pad(explode('x', $size, 2), 2, '');
    $relative = substr($ad, strlen($root) + 1);
    $ad = rtrim($GLOBALS['dummy_path'], '/') . '/assets/ad/'
        . implode('/', array_map('rawurlencode', explode('/', $relative)));
    if (!is_file($useinc)) {
        Runtime::error("Couldn't locate the ad template $inc.");
        return;
    }
    include $useinc;
}

function dummy_image($path, $params, $pathtype = 'URI')
{
    $basePath = $GLOBALS['dummy_assets_path'] . '/image/';
    $imgFn = getRandomFile($basePath, $path);
    if ($imgFn === null) {
        Runtime::error("Couldn't locate an image in assets/image/$path.");
        return;
    }
    $source = $basePath . $imgFn;
    $urlBase = rtrim($GLOBALS['dummy_path'], '/');
    if (trim($params) === '') {
        echo $pathtype === 'URI'
            ? $urlBase . '/assets/image/' . implode('/', array_map('rawurlencode', explode('/', $imgFn)))
            : $source;
        return;
    }
    $size = getimagesize($source);
    if ($size === false) {
        Runtime::error('The selected file is not a supported image.');
        return;
    }
    [$ox, $oy] = $size;
    $rx = $ry = $ax = $ay = 0;
    foreach (explode(',', str_replace(' ', '', $params)) as $param) {
        if (preg_match('/^(\d{0,5})x(\d{0,5})$/', $param, $m)) {
            $rx = (int) $m[1];
            $ry = (int) $m[2];
            if ($rx > MAX_WIDTH_PX) {
                $rx = 0;
            }
            if ($ry > MAX_HEIGHT_PX) {
                $ry = 0;
            }
        } elseif (preg_match('/^([\d.]+):([\d.]+)$/', $param, $m)) {
            $ax = (float) $m[1];
            $ay = (float) $m[2];
        }
    }
    if ($rx > 0 && $ry > 0) {
        $ax = $rx;
        $ay = $ry;
    }
    $cx = (float) $ox;
    $cy = (float) $oy;
    if ($ax > 0 && $ay > 0) {
        $ratio = $ax / $ay;
        if ($ox / $oy >= $ratio) {
            $cx = $oy * $ratio;
        } else {
            $cy = $ox / $ratio;
        }
    }
    $ix = $rx > 0 ? $rx : ($ry > 0 ? (int) floor($cx * $ry / $cy) : (int) floor($cx));
    $iy = $ry > 0 ? $ry : ($rx > 0 ? (int) floor($cy * $rx / $cx) : (int) floor($cy));
    if ($ix < 1 || $iy < 1 || (($rx > 0 || $ry > 0) && ($ix > MAX_WIDTH_PX || $iy > MAX_HEIGHT_PX))) {
        Runtime::error('Image dimensions must be positive and within MAX_WIDTH_PX and MAX_HEIGHT_PX.');
        return;
    }
    $ext = strtolower(pathinfo($imgFn, PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'gif', 'png'], true)) {
        Runtime::error('Image resizing supports JPEG, GIF, and PNG files.');
        return;
    }

    // Include the source path and modification time to avoid collisions and stale crops.
    $fingerprint = substr(hash('sha256', $imgFn . ':' . filemtime($source) . ':' . filesize($source)), 0, 16);
    $cacheName = getCacheFilename(pathinfo($imgFn, PATHINFO_FILENAME)) . "_{$fingerprint}_{$ix}x{$iy}.$ext";
    $cachePath = rtrim($GLOBALS['dummy_cache_path'], '/\\');
    $cached = $cachePath . '/' . $cacheName;
    if (!is_file($cached) || IGNORE_CACHE) {
        if (!is_dir($cachePath) && !@mkdir($cachePath, 0775, true) && !is_dir($cachePath)) {
            Runtime::error('The image cache directory could not be created.');
            return;
        }
        if (!is_writable($cachePath)) {
            Runtime::error('The image cache directory must be writable.');
            return;
        }
        $img = imagecreatefromstring(file_get_contents($source));
        if ($img === false) {
            Runtime::error('The selected image could not be decoded.');
            return;
        }
        $output = getTrueColorImg($img, $ix, $iy, $ext);
        // Crop and resize in one pass, without an intermediate full-size crop.
        imagecopyresampled($output, $img, 0, 0, (int) floor(($ox - $cx) / 2), (int) floor(($oy - $cy) / 2),
            $ix, $iy, max(1, (int) floor($cx)), max(1, (int) floor($cy)));
        $temporary = tempnam($cachePath, '.dummy-');
        if ($temporary === false) {
            Runtime::error('A temporary image cache file could not be created.');
            return;
        }
        try {
            $saved = match ($ext) {
                'jpg', 'jpeg' => imagejpeg($output, $temporary, JPEG_QUALITY),
                'gif' => imagegif($output, $temporary),
                'png' => imagepng($output, $temporary),
            };
            if (!$saved || !rename($temporary, $cached)) {
                Runtime::error('The generated image could not be saved.');
                return;
            }
            chmod($cached, 0664);
        } finally {
            if (is_file($temporary)) {
                unlink($temporary);
            }
        }
        // GdImage objects release memory automatically on supported PHP versions.
        unset($img, $output);
    }
    echo $pathtype === 'URI' ? $urlBase . '/cache/' . $cacheName : $cached;
}

function getTrueColorImg($img, $ix, $iy, $ext)
{
    $result = imagecreatetruecolor((int) $ix, (int) $iy);
    if ($ext === 'gif' || $ext === 'png') {
        $transparent = imagecolortransparent($img);
        if ($transparent >= 0) {
            $color = imagecolorsforindex($img, $transparent);
            $index = imagecolorallocate($result, $color['red'], $color['green'], $color['blue']);
            imagefill($result, 0, 0, $index);
            imagecolortransparent($result, $index);
        } elseif ($ext === 'png') {
            imagealphablending($result, false);
            imagefill($result, 0, 0, imagecolorallocatealpha($result, 0, 0, 0, 127));
            imagesavealpha($result, true);
        }
    }
    return $result;
}

function getCacheFilename($fn)
{
    return preg_replace('/\W/', '_', $fn);
}

function getRandomFile($basePath, $path)
{
    $files = getFilesRecursively($basePath, $path);
    if ($files === []) {
        return null;
    }
    $root = realpath($basePath);
    if (ADVANCED_RANDOM_IMAGES) {
        $unused = array_values(array_filter($files, static fn ($file) => !isset(Runtime::$usedImages[$root . '/' . $file])));
        if ($unused !== []) {
            $files = $unused;
        } else {
            foreach ($files as $file) {
                unset(Runtime::$usedImages[$root . '/' . $file]);
            }
        }
    }
    $file = $files[array_rand($files)];
    Runtime::$usedImages[$root . '/' . $file] = true;
    return $file;
}

function getFilesRecursively($basePath, $path = '', $files = [])
{
    $root = realpath($basePath);
    if ($root === false) {
        return $files;
    }
    $directory = Runtime::resolve($root, $path);
    if ($directory === null || !is_dir($directory) || !is_readable($directory)) {
        return $files;
    }
    $key = $root . ':' . $directory;
    if (!isset(Runtime::$fileLists[$key])) {
        $found = [];
        $visited = [];
        $scan = static function ($directory) use (&$scan, &$found, &$visited, $root): void {
            $resolved = realpath($directory);
            if ($resolved === false || isset($visited[$resolved]) || !is_readable($directory)) {
                return;
            }
            $visited[$resolved] = true;
            foreach (new DirectoryIterator($directory) as $entry) {
                $name = $entry->getFilename();
                if (str_starts_with($name, '.') || (HIDDEN_FOLDER_PREPEND !== '' && str_starts_with($name, HIDDEN_FOLDER_PREPEND))) {
                    continue;
                }
                $relative = substr($entry->getPathname(), strlen($root) + 1);
                if (Runtime::resolve($root, $relative) === null) {
                    continue;
                }
                if ($entry->isDir()) {
                    $scan($entry->getPathname());
                } elseif ($entry->isFile()) {
                    $found[] = str_replace(DIRECTORY_SEPARATOR, '/', $relative);
                }
            }
        };
        $scan($directory);
        sort($found);
        Runtime::$fileLists[$key] = $found;
    }
    return array_merge($files, Runtime::$fileLists[$key]);
}

function dummy($a, $internalcall = false)
{
    [$cmdraw, $params] = array_pad(explode($GLOBALS['delimiter'], $a, 2), 2, '');
    $cmd = strtolower($cmdraw);
    if ($cmd === 'ad') {
        dummy_ad($params);
    } elseif (str_starts_with($cmd, 'image')) {
        $path = substr($cmd, strlen('image'));
        dummy_image(rtrim($path, '/') . '/', $params, $internalcall ? 'FILE' : 'URI');
    } else {
        dummy_text($cmdraw, $params);
    }
}

function dumb_luck($p)
{
    $p = (string) $p;
    if (str_ends_with($p, '%')) {
        return rand(0, 99) < (int) $p;
    }
    if (in_array($p, ['', 'first', 'last', 'inner'], true)) {
        if (Runtime::$loops === []) {
            Runtime::error('Not inside dumb_luck.');
            return false;
        }
        $frame = Runtime::$loops[0];
        if ($p === '') {
            echo $frame['pos'] + 1;
            return;
        }
        return match ($p) {
            'first' => $frame['pos'] === 0,
            'last' => $frame['pos'] === $frame['count'] - 1,
            'inner' => $frame['pos'] !== 0 && $frame['pos'] !== $frame['count'] - 1,
        };
    }
    if (!is_numeric($p) && !preg_match('/^\d+-\d+$/', $p)) {
        Runtime::error('Wrong range given to dumb_luck.');
        return false;
    }

    // Keep the original call-site-based loop syntax, including nested loops.
    $call = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
    $ref = ($call['file'] ?? '') . ':' . ($call['line'] ?? 0) . ':' . $p;
    if (Runtime::$loops !== [] && Runtime::$loops[0]['ref'] === $ref) {
        if (++Runtime::$loops[0]['pos'] < Runtime::$loops[0]['count']) {
            return true;
        }
        array_shift(Runtime::$loops);
        return false;
    }
    if (is_numeric($p)) {
        $count = (int) $p;
    } else {
        [$min, $max] = array_map('intval', explode('-', $p, 2));
        $count = rand(min($min, $max), max($min, $max));
    }
    if ($count > 0) {
        array_unshift(Runtime::$loops, ['count' => $count, 'pos' => 0, 'ref' => $ref]);
        return true;
    }
    return false;
}

function dumb_question($p)
{
    [$cmd, $params] = array_pad(explode(':', $p, 2), 2, '');
    if ($cmd === 'order') {
        return dumb_luck($params);
    }
    $query = Runtime::$query ?? ($_GET ?? []);
    if ($cmd === 'url') {
        $params = str_replace(' ', '', $params);
        if ($params === '') {
            return $query === [] ? '' : '?' . http_build_query($query);
        }
        foreach (explode('&', $params) as $pair) {
            [$key, $value] = array_pad(explode('=', $pair, 2), 2, null);
            if (!array_key_exists($key, $query) || ($value !== null && $query[$key] != $value)) {
                return false;
            }
        }
        return true;
    }
    if ($cmd === 'segment') {
        $path = $query['dummyurl'] ?? Runtime::$requestPath ?? ($_SERVER['PATH_INFO'] ?? null);
        if ($path === null) {
            $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
            $script = $_SERVER['SCRIPT_NAME'] ?? '';
            if ($script !== '' && str_starts_with($path, $script . '/')) {
                $path = substr($path, strlen($script));
            }
        }
        if (!is_string($path)) {
            return false;
        }
        $segments = explode('/', '/' . ltrim($path, '/'));
        if ($params === '') {
            return $path;
        }
        if (!str_contains($params, '=')) {
            $n = (int) $params;
            if (isset($segments[$n])) {
                echo $segments[$n];
            }
            return;
        }
        foreach (explode('&', str_replace(' ', '', $params)) as $pair) {
            [$key, $value] = array_pad(explode('=', $pair, 2), 2, null);
            if (!isset($segments[$key]) || $value === null || $segments[$key] != $value) {
                return false;
            }
        }
        return true;
    }
    Runtime::error("Unknown command in dumb_question: $cmd.");
    return false;
}

// Preserve direct image URLs, e.g. /dummy/dummy.php?image@640x,16:9.
// Full paths prevent intercepting a host application's own dummy.php entry point.
if (isset($_SERVER['SCRIPT_FILENAME']) && realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    $command = preg_replace('/^dummyurl=[^&]*&?/', '', $_SERVER['QUERY_STRING'] ?? '');
    $command = rawurldecode($command);
    if (!preg_match('~^image(?:/[^@]*)?(?:@|$)~i', $command)) {
        http_response_code(400);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'Supply a Dummy image command, for example: ?image@640x,16:9';
        exit;
    }
    ob_start();
    dummy($command, true);
    $file = ob_get_clean();
    if (!is_file($file) || ($image = getimagesize($file)) === false) {
        http_response_code(400);
        header('Content-Type: text/plain; charset=UTF-8');
        echo strip_tags($file);
        exit;
    }
    header('Content-Type: ' . $image['mime']);
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
}
