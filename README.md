# Dummy

Dummy fills PHP templates with placeholder content and helps you test how a layout behaves when that content changes. Generate lorem ipsum at a chosen length, crop local images, show an element some of the time, or repeat a block a random number of times.

It works in plain PHP and Laravel. The original `dummy()`, `dumb_luck()`, and `dumb_question()` syntax is preserved, with no third-party runtime dependencies.

```php
<h1><?php dummy('text@words:5-9,title'); ?></h1>

<?php if (dumb_luck('50%')): ?>
    <img src="<?php dummy('image@640x,16:9'); ?>" alt="Example photograph">
<?php endif; ?>

<ul>
<?php while (dumb_luck('3-8')): ?>
    <li><?php dummy('text@headline'); ?></li>
<?php endwhile; ?>
</ul>
```

Reload the page to explore a different combination of text, images, and layout elements.

## Requirements

- PHP 8.3 or newer, within the PHP 8.x release line.
- The GD extension, with JPEG, PNG, and GIF support, for image generation.
- Composer 2 for package installation; optional for a folder-based installation.
- Laravel 12 or 13 for the optional Laravel integration.

Compatibility has been tested on actual PHP 8.3, 8.4, and 8.5 runtimes with both Laravel versions. See [verification results](docs/verification.md). Dummy does not need a database, Docker, or a native PHP session.

## Try the demo

[View the demo and documentation](https://kerns.github.io/dummy/).

To run the PHP demo locally, use this command from the Dummy directory:

```sh
php -S localhost:8000
```

Open [localhost:8000/demo.php](http://localhost:8000/demo.php). The demo includes installation instructions, working examples, copyable snippets, image thumbnails, and guidance for high-density displays. PHP needs write access to `dummy/cache`, or permission to create that directory.

The online examples stay the same between visits. Run the demo locally for fresh random content on each reload.

## Install with Composer

```sh
composer require --dev kerns/dummy
```

Composer loads the global helper functions automatically. Keep Dummy as a development dependency for prototyping and QA. Follow the Laravel or standalone PHP setup below to make the assets available to your application.

## Laravel and Blade

After the Composer installation, publish the bundled assets:

```sh
php artisan vendor:publish --tag=dummy-assets
```

Laravel discovers Dummy's service provider automatically. Assets are published to `public/vendor/dummy/assets`; generated crops go into `public/vendor/dummy/cache`. The web server needs permission to create and write to the cache directory. Dummy does not write into Composer's `vendor` directory.

Create `resources/views/dummy-demo.blade.php`:

```blade
<h1>@php(dummy('text@words:5-9,title'))</h1>
<p>@php(dummy('text@words:30-60'))</p>

@if (dumb_luck('50%'))
    <img src="@php(dummy('image@640x,16:9'))" alt="Example photograph">
@endif

<ul>
@while (dumb_luck('3-8'))
    <li>@php(dummy('text@headline'))</li>
@endwhile
</ul>
```

`dummy()` prints its result, so use `@php(...)` rather than Blade's escaped `{{ ... }}` output.

Add a route in `routes/web.php`:

```php
use Illuminate\Support\Facades\Route;

Route::view('/dummy-demo', 'dummy-demo');
```

Start your application with its normal development command, or `php artisan serve`, and open `/dummy-demo`.

To change asset paths or the URL prefix:

```sh
php artisan vendor:publish --tag=dummy-config
```

Edit `config/dummy.php` before publishing assets to the new location. Its defaults are:

```php
return [
    'url' => '/vendor/dummy',
    'assets_path' => public_path('vendor/dummy/assets'),
    'cache_path' => public_path('vendor/dummy/cache'),
];
```

The URL must serve the matching asset and cache directories. Rebuild cached configuration with `php artisan config:cache` after changes. Dummy resets its image-selection, loop, and file-pool state with Laravel's request lifecycle, including request rebinding in long-lived workers.

## Composer in a standalone PHP project

After installing the package, copy the assets into your public directory:

```sh
mkdir -p public/dummy
cp -R vendor/kerns/dummy/dummy/assets public/dummy/
```

Create `public/demo.php`:

```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

$dummy_path = '/dummy';
$dummy_assets_path = __DIR__ . '/dummy/assets';
$dummy_cache_path = __DIR__ . '/dummy/cache';
?>
<h1><?php dummy('text@headline'); ?></h1>
<p><?php dummy('text@words:30-60'); ?></p>
<img src="<?php dummy('image@640x,16:9'); ?>" alt="Example photograph">
```

Make the cache writable by PHP, then run `php -S localhost:8000 -t public` from your application directory. Open [localhost:8000/demo.php](http://localhost:8000/demo.php).

Generated lorem ipsum works without copying assets. Named text pools, images, and ads need the asset files.

## Plain PHP without Composer

Copy the entire `dummy` directory into your public web root and include it before calling the helpers:

```php
<?php require_once __DIR__ . '/dummy/dummy.php'; ?>
<h1><?php dummy('text@headline'); ?></h1>
<p><?php dummy('text@words:30-60'); ?></p>
```

Keep `dummy.php`, `Runtime.php`, `Lorem.php`, and the assets together. PHP needs write access to `dummy/cache`, or permission to create it. Use standard `<?php ... ?>` tags; legacy calling templates that use short tags still need PHP's `short_open_tag` setting.

For a different public location, set `$dummy_path`, `$dummy_assets_path`, and `$dummy_cache_path` before rendering. The first is a URL prefix; the other two are filesystem paths. Inside a function, set them through `$GLOBALS`.

## Generated lorem ipsum

Use a single count for an exact length, or a range for a random length on each call:

| Command | Output |
| --- | --- |
| `text@words:50` | Exactly 50 words |
| `text@words:30-60` | Between 30 and 60 words |
| `text@words:5-9,title` | Between 5 and 9 words, title cased |
| `text@sentences:3` | Exactly 3 sentences |
| `text@sentences:2-4` | Between 2 and 4 sentences |
| `text@paragraphs:2-3` | Between 2 and 3 paragraphs |

```php
<p><?php dummy('text@words:30-60'); ?></p>
<h2><?php dummy('text@words:5-9,title'); ?></h2>
```

Output is plain text from a built-in lorem corpus. `,title` capitalizes every word, including short words, and preserves punctuation. Without it, output uses normal sentence casing.

Ranges include both endpoints; reversed ranges work too. Zero prints nothing. Counts must be whole numbers, with per-call limits of 10,000 words, 1,000 sentences, or 100 paragraphs. Invalid counts print a Dummy error.

Each paragraph contains 3–6 sentences. Paragraphs are separated by blank lines; use your own markup or `white-space: pre-line` to display those breaks.

## Named text pools

Existing commands such as `text@headline`, `text@teaser`, `text@paragraph`, `text@author`, and `text@date` still select content from the corresponding files in `assets/text`. Add or edit these files to make your own pools.

Names without a colon remain file selections: `text@words` reads `words.txt`, while `text@words:50` generates text. Text pools and ad templates can execute PHP, as in the original release; use trusted local asset files.

## Images and ads

Image commands print a URL for an `<img>` element's `src` attribute:

| Command | Result |
| --- | --- |
| `image@480x320` | A 480×320 center crop |
| `image@640x,16:9` | A 16:9 crop, 640 pixels wide |
| `image@x200,16:9` | A 16:9 crop, 200 pixels high |
| `image/landscape@3:2` | A 3:2 crop selected from the landscape folder |
| `image/!avatar@150x150` | A 150×150 crop from the explicitly selected avatar folder |

Aspect-ratio-only commands crop at the source's available resolution. Request dimensions are image pixels. For high-density displays, request more pixels and set the CSS display size separately:

```php
<img
    src="<?php dummy('image/retina@1920x,16:9'); ?>"
    width="640"
    height="360"
    alt="Example photograph"
>
```

The bundled `image/retina` folder contains 2048×1536 sources. Some older photos have fewer pixels; enlarging them cannot restore missing detail.

JPEG, PNG, and GIF crops are supported. Transparency is preserved. Image selection avoids repeats until its pool is exhausted. Folders beginning with `!` are excluded from general selection and can be targeted explicitly.

Defaults include `JPEG_QUALITY = 90`, `MAX_WIDTH_PX = 2048`, and `MAX_HEIGHT_PX = 1536`. Set constants before loading Dummy or Composer's autoloader. Use a fresh image cache when changing JPEG quality. Crops are generated in one GD pass, cached per source and size, and written atomically.

The original direct endpoint, such as `/dummy/dummy.php?image@640x,16:9`, works in a drop-in installation. Laravel uses the published asset and cache URLs.

`dummy('ad@120x90')` prints complete ad markup using the templates in `assets/ad`. Legacy Flash markup remains opt-in through `$flash_ads = true`; current browsers do not support Flash.

## Probability, loops, and request helpers

```php
<?php if (dumb_luck('50%')): ?>
    <p>This appears about half the time.</p>
<?php endif; ?>

<?php while (dumb_luck('3-8')): ?>
    <p>Item <?php dumb_luck(''); ?></p>
<?php endwhile; ?>
```

Use `if` with percentages and `while` with counts or ranges. Nested loops keep their own positions. Inside a loop, `dumb_question('order:first')`, `dumb_question('order:last')`, and `dumb_question('order:inner')` return booleans; `dumb_luck('')` prints the one-based item number.

`dumb_question('url:mode=compact')` tests query parameters. `dumb_question('segment:1=news')` tests URL path segments, numbered from one. Laravel uses the current request; plain PHP uses `PATH_INFO` or `REQUEST_URI`. The optional legacy Apache rewrite's `dummyurl` parameter is also supported.

## Contributing

See the [contributor guide](CONTRIBUTING.md) for development setup, tests, and demo maintenance. The [verification report](docs/verification.md) lists tested versions and fresh-project installation results.

## License and credits

Dummy retains its original [Creative Commons Attribution-Share Alike 3.0 United States license](LICENSE). Image sources and attribution are listed in [CREDITS.md](CREDITS.md). The demo's bundled Highlight.js code uses its included BSD 3-Clause license.
