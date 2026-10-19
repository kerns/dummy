<?php
require_once __DIR__ . '/dummy/dummy.php';

$renderImagePreview = static function (string $command): void {
    ob_start();
    dummy($command);
    $url = htmlspecialchars(ob_get_clean(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    echo '<a class="image-preview" href="' . $url . '"><img src="' . $url
        . '" alt="' . $url . '" title="' . $url . '" loading="lazy" decoding="async"></a>';
};

$renderCodeExample = static function (string $code, string $label = 'PHP + HTML', string $language = 'php-template'): void {
    $escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $class = $language === 'text' ? 'no-highlight' : 'language-' . $language;
    echo '<div class="snippet code-snippet"><div class="code-toolbar"><span class="code-language">'
        . $escape($label) . '</span><button class="copy-code" type="button" data-copy-code aria-label="Copy '
        . $escape($label) . ' example" aria-live="polite">Copy</button></div><pre tabindex="0"><code class="'
        . $escape($class) . '">' . $escape(trim($code)) . '</code></pre></div>';
};
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dummy — prototyping toolkit</title>
  <meta name="description" content="Dummy is a toolkit for rapid prototyping and QA.">
  <meta name="author" content="David Kerns">

  <link href="demo_files/demo.css" rel="stylesheet">

</head>

<body id="top">
<a class="skip-link" href="#main">Skip to examples</a>
<div class="topbar">
  <div class="topbar-inner">
    <a class="wordmark" href="#top">Dummy.</a>
    <nav class="topbar-links" aria-label="Demo links">
      <a href="https://github.com/kerns/dummy">GitHub ↗</a>
    </nav>
  </div>
</div>
<div class="page-layout">
  <aside class="page-nav">
    <nav aria-label="On this page">
      <p class="nav-label">On this page</p>
      <a href="#getting-started">Getting started</a>
      <a class="nav-sub" href="#install-composer">Composer</a>
      <a class="nav-sub" href="#install-laravel">Laravel &amp; Blade</a>
      <a class="nav-sub" href="#install-php">Plain PHP</a>
      <a href="#assets">Assets</a>
      <a class="nav-sub" href="#dummy-text">Text</a>
      <a class="nav-sub" href="#generated-text">Generated lorem</a>
      <a class="nav-sub" href="#dummy-images">Images</a>
      <a class="nav-sub" href="#image-quality">Image quality</a>
      <a class="nav-sub" href="#dummy-ads">Ads</a>
      <a href="#luck">Probability &amp; loops</a>
      <a href="#complete-example">Complete example</a>
      <a href="#queries">Request queries</a>
    </nav>
  </aside>
  <div class="content-column">

<main id="main">

  <header class="page-header">
    <p class="eyebrow">For rapid prototyping &amp; QA</p>
    <h1>Welcome to Dummy.</h1>
  </header>

  <p class="hero">Fill PHP templates with realistic placeholder content, vary the length of an article, or show an image only some of the time. Reload the page to see how your layout handles a different combination.</p>
  <p class="hero">Install with <a href="#install-composer">Composer</a>, use it in <a href="#install-laravel">Laravel and Blade</a>, or <a href="#install-php">copy the folder into a plain PHP project</a>. The instructions and working examples are all here.</p>
  <?php if ($GLOBALS['dummy_static_demo'] ?? false): ?>
  <p class="note snapshot-note">This is a generated snapshot of the PHP demo. Run Dummy locally to get fresh random content on every request.</p>
  <?php endif; ?>

<section>
<h2 id="getting-started">Getting Started</h2>

<article>
<h3>Requirements</h3>
<p>Use PHP 8.3 or newer with the GD extension enabled. Composer 2 is needed for package installation. The Laravel integration supports Laravel 12 and 13. Dummy needs no database, Docker setup, or native PHP session.</p>
<p>To run the demo locally, use the following command from the Dummy directory, then open <a href="http://localhost:8000/demo.php">localhost:8000/demo.php</a>.</p>
<?php $renderCodeExample('php -S localhost:8000', 'Terminal', 'text'); ?>
</article>

<article>
<h3 id="install-composer">Install with Composer</h3>
<p>From your application directory, install Dummy as a development dependency. Composer loads <code>dummy()</code>, <code>dumb_luck()</code>, and <code>dumb_question()</code> automatically.</p>
<?php $renderCodeExample(<<<'CODE'
composer require --dev kerns/dummy
CODE, 'Terminal', 'text'); ?>
<p>Next, follow the <a href="#install-laravel">Laravel setup</a> or the <a href="#install-composer-php">standalone Composer setup</a> below.</p>
</article>

<article>
<h3 id="install-laravel">Laravel &amp; Blade</h3>
<p>After installing with Composer, run this command from your Laravel application's root directory:</p>
<?php $renderCodeExample('php artisan vendor:publish --tag=dummy-assets', 'Terminal', 'text'); ?>
<p>Laravel discovers Dummy's service provider automatically. The command publishes images, text pools, and ad templates to <code>public/vendor/dummy/assets</code>. Generated crops go into <code>public/vendor/dummy/cache</code>, which PHP creates automatically. The web server needs permission to create and write to that cache directory.</p>
<p>Create <code>resources/views/dummy-demo.blade.php</code> and use the helpers directly in the view:</p>
<?php $renderCodeExample(<<<'CODE'
<h1>@php(dummy('text@words:5-9,title'))</h1>

@if (dumb_luck('50%'))
    <img src="@php(dummy('image@640x,16:9'))" alt="Example image">
@endif

<ul>
@while (dumb_luck('3-8'))
    <li>@php(dummy('text@headline'))</li>
@endwhile
</ul>
CODE, 'Blade'); ?>
<p><code>dummy()</code> prints its result. Use <code>@php(...)</code> for it rather than Blade's escaped <code>{{ ... }}</code> output. The <code>50%</code> condition shows the image about half the time, and the loop produces between three and eight items.</p>
<p>Add a route to <code>routes/web.php</code>:</p>
<?php $renderCodeExample(<<<'CODE'
use Illuminate\Support\Facades\Route;

Route::view('/dummy-demo', 'dummy-demo');
CODE, 'PHP', 'php'); ?>
<p>Start Laravel with your application's normal development command, or run <code>php artisan serve</code>. Open <a href="http://localhost:8000/dummy-demo">localhost:8000/dummy-demo</a> when using the default local port. Laravel serves the published assets and cached crops from its public directory.</p>
</article>

<article>
<h3 id="install-composer-php">Composer in a standalone PHP project</h3>
<p>After the Composer installation above, put Dummy's assets under your public web root. For a project with a <code>public</code> directory, run:</p>
<?php $renderCodeExample(<<<'CODE'
mkdir -p public/dummy
cp -R vendor/kerns/dummy/dummy/assets public/dummy/
CODE, 'Terminal', 'text'); ?>
<p>Create <code>public/demo.php</code> with the following contents. The three paths connect Dummy to the assets and cache in your public directory:</p>
<?php $renderCodeExample(<<<'CODE'
<?php
require_once __DIR__ . '/../vendor/autoload.php';

$dummy_path = '/dummy';
$dummy_assets_path = __DIR__ . '/dummy/assets';
$dummy_cache_path = __DIR__ . '/dummy/cache';
?>
<h1><?php dummy('text@headline'); ?></h1>
<p><?php dummy('text@words:30-60'); ?></p>
<img src="<?php dummy('image@640x,16:9'); ?>" alt="Example image">
CODE); ?>
<p>Make sure PHP can create <code>public/dummy/cache</code>, or create it yourself and give the web server write access. Start the server from your application directory and open <a href="http://localhost:8000/demo.php">localhost:8000/demo.php</a>:</p>
<?php $renderCodeExample('php -S localhost:8000 -t public', 'Terminal', 'text'); ?>
<p>If your project serves files from another directory, adjust the include path and asset locations to match that web root. Generated lorem ipsum works without copying assets; named text pools, images, and ads need the published files.</p>
</article>

<article>
<h3 id="install-php">Plain PHP without Composer</h3>
<ol>
  <li>Copy the entire <code>dummy</code> directory into your public web root. Keep <code>dummy.php</code>, <code>Runtime.php</code>, <code>Lorem.php</code>, and the <code>assets</code> directory together.</li>
  <li>Create a PHP template in that web root and include Dummy before calling its helpers:</li>
</ol>
<?php $renderCodeExample(<<<'CODE'
<?php require_once __DIR__ . '/dummy/dummy.php'; ?>

<h1><?php dummy('text@headline'); ?></h1>
<p><?php dummy('text@words:30-60'); ?></p>

<?php if (dumb_luck('50%')): ?>
    <img src="<?php dummy('image@640x,16:9'); ?>" alt="Example image">
<?php endif; ?>
CODE); ?>
<p>Make <code>dummy/cache</code> writable by PHP. Dummy creates the directory if its parent is writable. Use <code>php -S localhost:8000</code> from that web root, or your usual PHP development server, and open the template in your browser.</p>
<p>The image below demonstrates image cropping and caching. It uses a larger source image so it stays sharp on high-density displays.</p>
<div class="snippet img-snippet" id="installation-image">
  <img src="<?php dummy('image/retina@1500x,4:3'); ?>" width="500" height="375" alt="A randomly selected demo photograph" />
  <p class="note">
  <?php
  $cachePath = $GLOBALS['dummy_cache_path'];
  if (is_dir($cachePath) && is_writable($cachePath)) {
      echo 'Success. Images are rendering and the cache directory is writable.';
  } else {
      echo 'PHP needs write access to the configured image cache directory.';
  }
  ?>
  </p>
</div>
</article>

<article>
<h3 id="configuration">Paths, cache &amp; image settings</h3>
<p>In Laravel, publish the configuration if you need different asset paths or a URL prefix:</p>
<?php $renderCodeExample('php artisan vendor:publish --tag=dummy-config', 'Terminal', 'text'); ?>
<p>Edit <code>config/dummy.php</code>. These are the defaults:</p>
<?php $renderCodeExample(<<<'CODE'
<?php
return [
    'url' => '/vendor/dummy',
    'assets_path' => public_path('vendor/dummy/assets'),
    'cache_path' => public_path('vendor/dummy/cache'),
];
CODE, 'PHP', 'php'); ?>
<p>Set custom paths before publishing assets. The URL must serve the matching asset and cache directories. If configuration is cached, run <code>php artisan config:cache</code> after changing it.</p>
<p>Outside Laravel, set <code>$dummy_path</code>, <code>$dummy_assets_path</code>, and <code>$dummy_cache_path</code> before rendering, as in the Composer example above. The include path is a filesystem path; <code>$dummy_path</code> is the public URL prefix. Inside a function, set these through <code>$GLOBALS</code>.</p>
<p>Optional image constants such as <code>JPEG_QUALITY</code> (default <code>90</code>), <code>MAX_WIDTH_PX</code> (default <code>2048</code>), and <code>MAX_HEIGHT_PX</code> (default <code>1536</code>) must be set before loading Dummy. In Laravel or a Composer project, that means before loading <code>vendor/autoload.php</code>. Use a fresh cache directory when changing JPEG quality so existing crops are not reused.</p>
</article>

<article>
<h3>Troubleshooting</h3>
<ul>
  <li>If PHP cannot find Dummy, check the filesystem path in <code>require_once</code>. Use <code>__DIR__</code> so it does not depend on the current working directory.</li>
  <li>If images fail to load, confirm that the configured public URL points to the assets and cache directories, and that PHP can write to the cache.</li>
  <li>If GD is missing, enable the GD extension for the PHP runtime serving the page. Your terminal and web server can use different PHP configurations.</li>
  <li>Use standard <code>&lt;?php ... ?&gt;</code> tags. Old templates using <code>&lt;? ... ?&gt;</code> need PHP's <code>short_open_tag</code> setting; new examples do not.</li>
</ul>
<p>Text pools and custom ad templates can contain executable PHP. Use trusted local files. Keep Dummy as a development dependency for prototyping and QA.</p>
</article>
</section>

<section>
<h2 id="assets">Dummy Assets</h2>
<p>The ability to select, manipulate, and insert different assets into your front-end project is a key feature of Dummy. All of the assets available to Dummy live in a folder located at <b>dummy/assets/</b>. It's a good idea to poke around in here and see how things are structured. The simple syntax used to request different assets relates very much to the structure of this directory.</p>

<article>
<h3 id="dummy-text">Dummy Text</h3>
<p>Dummy makes it easy to insert random strings of Lorem Ipsum that correspond to commonly needed lengths and formats. For example, the following snippet of code will produce a random string of Lorem Ipsum approximately the length of a headline.</p>

<div class="snippet code-snippet">
  <div class="code-toolbar"><span class="code-language">PHP + HTML</span><button class="copy-code" type="button" data-copy-code aria-label="Copy PHP and HTML example" aria-live="polite">Copy</button></div>
  <pre id="code-example-2" tabindex="0"><code class="language-php-template">&lt;?php dummy("text@headline") ?&gt;</code></pre>
</div>

<p>The exact result will be different every time you reload your document, but it should look something like this...</p>

<div class="snippet text-snippet">
  <h4><?php dummy("text@headline") ?></h4>
</div>

<p>The following table is a catalog of Dummy's default assets, the snippet of code used to insert the asset, and a sample of actual output.</p>

<div class="snippet table-snippet">
<table summary="Dummy Text Assets" class="text-table">
    <thead>
      <tr>
        <th scope="col" class="objective">Objective</th>
        <th scope="col">Dummy Code</th>
        <th scope="col" class="output">Output</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Headline</td>
        <td><code>&lt;?php dummy("text@headline") ?&gt;</code></td>
        <td><?php dummy("text@headline") ?></td>
      </tr>
        <tr>
          <td>Teaser</td>
          <td><code>&lt;?php dummy("text@teaser") ?&gt;</code></td>
          <td><?php dummy("text@teaser") ?></td>
        </tr>
        <tr>
          <td>Short Teaser</td>
          <td><code>&lt;?php dummy("text@short-teaser") ?&gt;</code></td>
          <td><?php dummy("text@short-teaser") ?></td>
        </tr>
        <tr>
          <td>Long Teaser</td>
          <td><code>&lt;?php dummy("text@long-teaser") ?&gt;</code></td>
          <td><?php dummy("text@long-teaser") ?></td>
        </tr>
        <tr>
          <td>Item</td>
          <td><code>&lt;?php dummy("text@item") ?&gt;</code></td>
          <td><?php dummy("text@item") ?></td>
        </tr>
        <tr>
          <td>Date</td>
          <td><code>&lt;?php dummy("text@date") ?&gt;</code></td>
          <td><?php dummy("text@date") ?></td>
        </tr>
        <tr>
          <td>Time</td>
          <td><code>&lt;?php dummy("text@time") ?&gt;</code></td>
          <td><?php dummy("text@time") ?></td>
        </tr>
        <tr>
          <td>Time ago (relative)</td>
          <td><code>&lt;?php dummy("text@time-ago") ?&gt;</code></td>
          <td><?php dummy("text@time-ago") ?></td>
        </tr>
        <tr>
          <td>Author</td>
          <td><code>&lt;?php dummy("text@author") ?&gt;</code></td>
          <td><?php dummy("text@author") ?></td>
        </tr>
        <tr>
          <td>Username</td>
          <td><code>&lt;?php dummy("text@username") ?&gt;</code></td>
          <td><?php dummy("text@username") ?></td>
        </tr>
        <tr>
          <td>City</td>
          <td><code>&lt;?php dummy("text@city") ?&gt;</code></td>
          <td><?php dummy("text@city") ?></td>
        </tr>
        <tr>
          <td>Paragraph</td>
          <td><code>&lt;?php dummy("text@paragraph") ?&gt;</code></td>
          <td><?php dummy("text@paragraph") ?></td>
        </tr>
    </tbody>
</table>
<p class="note"><strong>Hint:</strong> Inside of <strong>dummy/assets/text/</strong> you'll find a directory full of flat text files. These are the basis for organizing different text formats used by Dummy. You can edit these or add your own as you like.</p>
</div>
</article>

<article>
<h3 id="generated-text">Lorem ipsum at any length</h3>
<p>Keep using named text pools for headlines and other curated content, or generate fresh lorem ipsum by specifying a count. Use a single number for an exact length, or a range for a different length on each call.</p>

<div class="snippet code-snippet">
  <div class="code-toolbar"><span class="code-language">PHP + HTML</span><button class="copy-code" type="button" data-copy-code aria-label="Copy PHP and HTML example" aria-live="polite">Copy</button></div>
  <pre tabindex="0"><code class="language-php-template">&lt;?php dummy("text@words:50") ?&gt;
&lt;?php dummy("text@words:30-60") ?&gt;
&lt;?php dummy("text@words:5-9,title") ?&gt;
&lt;?php dummy("text@sentences:2-4") ?&gt;
&lt;?php dummy("text@paragraphs:2-3") ?&gt;</code></pre>
</div>

<div class="snippet table-snippet">
<table class="generated-text-table" aria-label="Generated lorem ipsum examples">
  <thead><tr><th scope="col">Length</th><th scope="col">Dummy Code</th><th scope="col">Live output</th></tr></thead>
  <tbody>
    <tr><td>Exactly 50 words</td><td><code>&lt;?php dummy("text@words:50") ?&gt;</code></td><td data-lorem-example="words:50"><?php dummy("text@words:50") ?></td></tr>
    <tr><td>30–60 words</td><td><code>&lt;?php dummy("text@words:30-60") ?&gt;</code></td><td data-lorem-example="words:30-60"><?php dummy("text@words:30-60") ?></td></tr>
    <tr><td>5–9 words, title case</td><td><code>&lt;?php dummy("text@words:5-9,title") ?&gt;</code></td><td data-lorem-example="words:5-9,title"><?php dummy("text@words:5-9,title") ?></td></tr>
    <tr><td>2–4 sentences</td><td><code>&lt;?php dummy("text@sentences:2-4") ?&gt;</code></td><td data-lorem-example="sentences:2-4"><?php dummy("text@sentences:2-4") ?></td></tr>
    <tr><td>2–3 paragraphs</td><td><code>&lt;?php dummy("text@paragraphs:2-3") ?&gt;</code></td><td class="lorem-paragraphs" data-lorem-example="paragraphs:2-3"><?php dummy("text@paragraphs:2-3") ?></td></tr>
  </tbody>
</table>
<p class="note">Output is plain text. Add <code>,title</code> to capitalize every word; omit it for normal sentence casing. Paragraphs are separated by blank lines; use your own markup or CSS to display those breaks. Zero returns no text. Ranges include both endpoints. Limits per call: 10,000 words, 1,000 sentences, or 100 paragraphs. No extra packages or asset publishing needed.</p>
</div>
</article>

<article>
<h3 id="dummy-images">Dummy Images</h3>
<p>Dummy makes it easy to crop and size placeholder images on the fly. It ships with a starter pack of high-quality, newsworthy, Creative Commons licensed images, but like other assets it's easy to edit or expand these according to the needs of your project.</p>
<p>The syntax for requesting an image is flexible. In a very basic example, you request an image with specific dimensions...</p>

<div class="snippet code-snippet">
  <div class="code-toolbar"><span class="code-language">PHP + HTML</span><button class="copy-code" type="button" data-copy-code aria-label="Copy PHP and HTML example" aria-live="polite">Copy</button></div>
  <pre id="code-example-3" tabindex="0"><code class="language-php-template">&lt;img src="&lt;?php dummy("image@480x320") ?&gt;" /&gt;</code></pre>
</div>

<p>...and the following is returned:</p>

<div class="snippet img-snippet">
  <img src="<?php dummy("image@480x320") ?>" width="480" height="320" alt="A 480 by 320 pixel crop" />
  <p class="note">This example returns exactly 480×320 image pixels. At 480 CSS pixels wide, it is a 1× image; high-density screens benefit from a larger crop, as shown below.</p>
</div>

<p>You can specify exact pixel dimensions or just an aspect ratio. You can also combine these parameters. For example, you can choose to specify just the width together with an aspect ratio. Dummy will return the path to an image that conforms to both requirements.</p>

<div class="snippet code-snippet">
  <div class="code-toolbar"><span class="code-language">PHP + HTML</span><button class="copy-code" type="button" data-copy-code aria-label="Copy PHP and HTML example" aria-live="polite">Copy</button></div>
  <pre id="code-example-4" tabindex="0"><code class="language-php-template">&lt;img src="&lt;?php dummy("image@640x,16:9") ?&gt;" /&gt;</code></pre>
</div>

<p>Returns...</p>

<div class="snippet img-snippet">
  <img src="<?php dummy("image@640x,16:9") ?>" width="640" height="360" alt="A crop with a 16 to 9 aspect ratio" />
  <p class="note">Use the returned image URL in your <code>&lt;img&gt;</code> element's <code>src</code> attribute.</p>
</div>

<h4 id="image-quality">Sharp images on high-density screens</h4>
<p>Image dimensions are real pixels, not CSS display sizes. For a photo displayed at 640 pixels wide, request a larger crop and set its display width separately. The example below requests 1920×1080 pixels and displays them at 640×360, giving the browser three image pixels per CSS pixel.</p>
<?php $renderCodeExample(<<<'CODE'
<img
  src="<?php dummy('image/retina@1920x,16:9'); ?>"
  width="640"
  height="360"
  alt="A high-resolution demo photograph"
>
CODE); ?>
<div class="snippet img-snippet">
  <img src="<?php dummy('image/retina@1920x,16:9'); ?>" width="640" height="360" alt="A high-resolution demo photograph" loading="lazy" />
  <p class="note">The bundled <code>image/retina</code> folder contains 2048×1536 source photos. Some older landscape photos are only 1024–1150 pixels wide. A larger request cannot restore detail missing from the original.</p>
</div>
<p>JPEG crops are encoded at quality <code>90</code> by default. That adds some compression, but reducing the pixel dimensions is the main reason the earlier demo looked soft on a high-density screen. Supply a sufficiently large source and request enough pixels for the intended display size. Aspect-ratio-only commands such as <code>image@16:9</code> crop at the source's available resolution.</p>

<p>The following table provides some clues as to how you can structure your image requests.</p>

<div class="snippet table-snippet">
<table summary="Dummy Image Assets" class="image-table">
    <thead>
      <tr>
        <th scope="col" class="objective">Objective</th>
        <th scope="col" class="code">Dummy Code</th>
        <th scope="col" class="output">Output</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>100px square <span>Classic. Square.</span></td>
        <td><code>&lt;?php&nbsp;dummy("image@100x100")&nbsp;?&gt;</code></td>
        <td><?php $renderImagePreview('image@100x100'); ?></td>
      </tr>
      <tr>
        <td>100px square (Part II) <span>A rather pointless way of generating the same result as above. Proving a point here.</span></td>
        <td><code>&lt;?php&nbsp;dummy("image@100x,1:1")&nbsp;?&gt;</code></td>
        <td><?php $renderImagePreview('image@100x,1:1'); ?></td>
      </tr>
      <tr>
        <td>16:9 <span>With a height of 200px. Pixel dimensions and aspect ratios must be separated with a comma.</span></td>
        <td><code>&lt;?php&nbsp;dummy("image@16:9,x200")&nbsp;?&gt;</code></td>
        <td><?php $renderImagePreview('image@16:9,x200'); ?></td>
      </tr>
      <tr>
        <td>16:9 (Part II) <span>Same as above. It doesn't matter which order you specify ratio or pixel dimensions.</span></td>
        <td><code>&lt;?php&nbsp;dummy("image@x200,16:9")&nbsp;?&gt;</code></td>
        <td><?php $renderImagePreview('image@x200,16:9'); ?></td>
      </tr>
      <tr>
        <td>3:2 <span>The Photographer's aspect! We'll ask for assets from a specific subdirectory of &lsquo;dummy/assets/image/&rsquo; here to improve the odds of getting a suitable image.</span></td>
        <td><code>&lt;?php&nbsp;dummy("image/landscape@3:2")&nbsp;?&gt;</code></td>
        <td><?php $renderImagePreview('image/landscape@3:2'); ?></td>
      </tr>
      <tr>
        <td>!Avatar <span>Subdirectories of &lsquo;dummy/assets/image/&rsquo; that begin with a &lsquo;!&rsquo; are exempted from normal random (and recursive) selection. You have to target them specifically. This is good.</span></td>
        <td><code>&lt;?php dummy("image/!avatar@150x150") ?&gt;</code></td>
        <td><?php $renderImagePreview('image/!avatar@150x150'); ?></td>
      </tr>
    </tbody>
</table>
<p class="note"><strong>Note:</strong> When specifying dimensions, either as an exact pixel measurement or as an aspect ratio, describe the width first and height second (<b>Width</b>&nbsp;x&nbsp;<b>Height</b>).</p>
</div>

<p>The basic model for how Dummy selects image assets is semi-random and recursive. It begins looking for assets on the root level of <b>"assets/image"</b> and drills down, only ignoring sub-folders with names that begin with a &lsquo;<b>!</b>&rsquo;. Dummy also works in the background to track which image was most recently selected, in order to minimize the odds of inserting the same image in succession or in close proximity.</p>
</article>

<article>
<h3 id="dummy-ads">Dummy Ads</h3>
<p>Inside of <b>/dummy/assets/ad</b> you'll find a host of folders representing many of the popular formats <a href="http://www.iab.net/guidelines/508676/508767/displayguidelines">as defined by the IAB</a>. The dummy code used to request and insert an ad follows the now familiar model.</p>

<div class="snippet code-snippet">
  <div class="code-toolbar"><span class="code-language">PHP + HTML</span><button class="copy-code" type="button" data-copy-code aria-label="Copy PHP and HTML example" aria-live="polite">Copy</button></div>
  <pre id="code-example-5" tabindex="0"><code class="language-php-template">&lt;?php dummy("ad@120x90") ?&gt;</code></pre>
</div>

<p>Returns...</p>

<div class="snippet img-snippet">
  <?php dummy("ad@120x90") ?>
</div>

<p>Note that a request for an ad returns a pre-formatted block of embed code containing an ad, as opposed to just the URL path to the ad (as is done with images). The reason for this is that we often need blocks of repetitious, ancillary markup to surround ad placements, as is the case with regard to advertising in the Adobe&reg; Flash&reg; format.</p>
<p>The default embed code for both standards compliant image based ads and ads in the Flash format can be found on the root level of <b>/dummy/assets/ad</b> in the form of two files &mdash; <b>image.embed.php</b> and <b>flash.embed.php</b> respectively. You can override the use of these defaults by placing an edited copy of one or both of these files in the folder of the format you wish to override.</p>

<p>The folders containing different formats are also used to specify their dimensions (WxH). Dummy parses these folder names and interprets them as variables, which in turn can be used to set the width and height values of ads as they are parsed into the embed code. If you don't feel it necessary to pass these values to your embed code, you're free to add new formats in folders and name them however you like &mdash; just remember to create a custom embed for these formats.</p>

<p>You may find it useful to enable or disable the insertion of Flash format ads under different testing scenarios. Set <code>$flash_ads = true</code> before rendering to include legacy Flash ads. The default is disabled, and current browsers do not support Flash.</p>
</article>
</section>

<section>
<h2 id="luck">Dumb Luck</h2>
<p>The ability to insert randomly selected assets into a layout is great, but it's Dummy's simple logic for controlling probability and creating loop ranges that make it possible to flesh out highly variable, asset rich layouts with very little code. The basis for this logic is contained in a function called <b>dumb_luck</b>.</p>

<article>
<h3>Controlling Probability</h3>
<p>A basic building block of Dummy driven layouts, <b>dumb_luck</b> can be used to influence the probability that something will happen when the document is rendered. This helps answer the age old question, <em>&ldquo;What does this layout look like with or without <b>X</b> ?&rdquo;</em>, where X could be an image, a special announcement, or a block of JavaScript that triggers some other chain of events.</p>

<div class="snippet code-snippet">
  <div class="code-toolbar"><span class="code-language">PHP + HTML</span><button class="copy-code" type="button" data-copy-code aria-label="Copy PHP and HTML example" aria-live="polite">Copy</button></div>
  <pre id="code-example-6" tabindex="0"><code class="language-php-template">&lt;?php if (dumb_luck("50%")): ?&gt;
  There is a 50/50 chance of this happening.
&lt;?php endif ?&gt;</code></pre>
</div>

<p>Jazz up your <b>dumb_luck</b> logic with fallback or alternative outcomes using standard PHP if/else statement structure.</p>

<div class="snippet code-snippet">
  <div class="code-toolbar"><span class="code-language">PHP + HTML</span><button class="copy-code" type="button" data-copy-code aria-label="Copy PHP and HTML example" aria-live="polite">Copy</button></div>
  <pre id="code-example-7" tabindex="0"><code class="language-php-template">&lt;?php if (dumb_luck("95%")): ?&gt;
  Pretty damn likely to happen.
&lt;?php else: ?&gt;
  This is a lot less likely.
&lt;?php endif ?&gt;</code></pre>
</div>

<p>You can also nest <b>dumb_luck</b> logic as needed, to test and explore more complex outcomes.</p>

<div class="snippet code-snippet">
  <div class="code-toolbar"><span class="code-language">PHP + HTML</span><button class="copy-code" type="button" data-copy-code aria-label="Copy PHP and HTML example" aria-live="polite">Copy</button></div>
  <pre id="code-example-8" tabindex="0"><code class="language-php-template">&lt;?php if (dumb_luck("50%")): ?&gt;
  &lt;?php if (dumb_luck("50%")): ?&gt;
    Heads
  &lt;?php else: ?&gt;
    Tails
  &lt;?php endif ?&gt;
&lt;?php else: ?&gt;
  &lt;?php if (dumb_luck("33%")): ?&gt;
    Rock
  &lt;?php elseif (dumb_luck("33%")): ?&gt;
    Paper
  &lt;?php else: ?&gt;
    Scissors
  &lt;?php endif ?&gt;
&lt;?php endif ?&gt;</code></pre>

<p class="note">For the record... <b><?php if (dumb_luck("50%")): ?><?php if (dumb_luck("50%")): ?>Heads<?php else: ?>Tails<?php endif ?><?php else: ?><?php if (dumb_luck("50%")): ?>Rock<?php elseif (dumb_luck("50%")): ?>Paper<?php else: ?>Scissors<?php endif ?><?php endif ?></b>!</p>
</div>
</article>

<article>
<h3>Creating Loop Ranges</h3>

<div class="snippet img-snippet thumb-snippet">
	<?php while (dumb_luck("50-100")): ?>
	<a href="#"><img src="<?php dummy("image/landscape@105x105")?>" width="35" height="35" alt="A thumbnail..." /></a>
  <?php endwhile ?>
	<p class="note">Dumb Luck's loop range takes whatever content you place inside of it and loops it within a range of two numbers that you specify. Inside of the loop range above, a single request for a 35x35 pixel thumbnail.</p>
</div>

<p>Loop ranges make it possible to generate massive amounts of variable content very quickly. In the example above, there are two bits of Dummy code working together. A <b>dumb_luck</b> loop range of 50-100, and inside of that loop range, a single request for a 35x35 pixel thumbnail.</p>

<p>The thumbnail will be rendered no less than 50 times, and up to 100. The Dummy code used to achieve this is compact and human readable:</p>

<div class="snippet code-snippet">
  <div class="code-toolbar"><span class="code-language">PHP + HTML</span><button class="copy-code" type="button" data-copy-code aria-label="Copy PHP and HTML example" aria-live="polite">Copy</button></div>
  <pre id="code-example-9" tabindex="0"><code class="language-php-template">&lt;?php while (dumb_luck("50-100")): ?&gt;
  &lt;?php dummy("image@35x35") ?&gt;
&lt;?php endwhile ?&gt;</code></pre>
</div>

<p>Loop ranges are built on the same <b>dumb_luck</b> function used to control probability, only instead of passing in a <b>%</b> value we pass it a range of two numbers separated by a hyphen. These two values define the <em>minimum</em> and <em>maximum</em> number of times the loop should continue. In the example below, we ask for between 5 and 10 instances of a list item containing a headline.</p>

<div class="snippet code-snippet">
  <div class="code-toolbar"><span class="code-language">PHP + HTML</span><button class="copy-code" type="button" data-copy-code aria-label="Copy PHP and HTML example" aria-live="polite">Copy</button></div>
  <pre id="code-example-10" tabindex="0"><code class="language-php-template">&lt;ul&gt;
&lt;?php while (dumb_luck("5-10")): ?&gt;
  &lt;li&gt;&lt;?php dummy("text@headline") ?&gt;&lt;/li&gt;
&lt;?php endwhile ?&gt;
&lt;/ul&gt;</code></pre>
  <p class="note">Note that the PHP syntax used with dumb_luck requires &ldquo;WHILE&rdquo; in association with loop ranges.</p>
</div>

<p>The result is a simple UL containing between 5 and 10 list items:</p>

<div class="snippet text-snippet">
  <ul>
  <?php while (dumb_luck("5-10")): ?>
    <li><?php dummy("text@headline") ?></li>
  <?php endwhile ?>
  </ul>
</div>
</article>

<article>
<h3>Putting It All Together</h3>

<p>Taking the same list used in the previous example, imagine we needed to see how it would look with <em>some</em> of the items linked <em>some</em> of the time. To achieve this we'll nest some probability logic inside of our loop range.</p>

<div class="snippet code-snippet">
  <div class="code-toolbar"><span class="code-language">PHP + HTML</span><button class="copy-code" type="button" data-copy-code aria-label="Copy PHP and HTML example" aria-live="polite">Copy</button></div>
  <pre id="code-example-11" tabindex="0"><code class="language-php-template">&lt;ul&gt;
&lt;?php while (dumb_luck("5-10")): ?&gt;
  &lt;?php if (dumb_luck("50%")): ?&gt;
    &lt;li&gt;&lt;a href="#"&gt;&lt;?php dummy("text@headline") ?&gt;&lt;/a&gt;&lt;/li&gt;
  &lt;?php else: ?&gt;
    &lt;li&gt;&lt;?php dummy("text@headline") ?&gt;&lt;/li&gt;
  &lt;?php endif ?&gt;
&lt;?php endwhile ?&gt;
&lt;/ul&gt;</code></pre>
</div>

<p>Though the exact number will change every time you reload the page, roughly half of the items in the following list will be wrapped in a link. It's a very basic example, but one that uses all the core features of Dummy.</p>


<div class="snippet text-snippet">
  <ul><?php while (dumb_luck("5-10")): ?><?php if (dumb_luck("50%")): ?><li><a href="#"><?php dummy("text@headline") ?></a></li><?php else: ?><li><?php dummy("text@headline") ?></li><?php endif ?><?php endwhile ?></ul>
</div>

<p>When used together, asset generation, probability, and loop range logic can create highly variable layouts with very little effort in code. This makes it possible to quickly test how the change of an item, attribute or parameter in one place propagates on a range of different scales. It can provide new insights on the performance or design efficacy of your work at the earliest stages of a project, and every time you reload the page.</p>

<div class="snippet code-snippet">
  <div class="code-toolbar"><span class="code-language">PHP + HTML</span><button class="copy-code" type="button" data-copy-code aria-label="Copy PHP and HTML example" aria-live="polite">Copy</button></div>
  <pre id="complete-example" tabindex="0"><code class="language-php-template">&lt;h1&gt;&lt;?php dummy("text@headline") ?&gt;&lt;/h1&gt;
&lt;?php if (dumb_luck("25%")): ?&gt;
  &lt;p class="intro"&gt;&lt;?php dummy("text@long-teaser") ?&gt;&lt;/p&gt;
&lt;?php endif ?&gt;
&lt;p&gt;By &lt;?php dummy("text@author") ?&gt; | Published &lt;?php dummy("text@date") ?&gt;&lt;/p&gt;
&lt;?php while (dumb_luck("3-8")): ?&gt;
  &lt;p&gt;&lt;?php dummy("text@paragraph") ?&gt;&lt;/p&gt;
&lt;?php endwhile ?&gt;
&lt;?php if (dumb_luck("75%")): ?&gt;
  &lt;p&gt;&lt;b&gt;Related Story:&lt;/b&gt; &lt;a href="#"&gt;&lt;?php dummy("text@headline") ?&gt;&lt;/a&gt;&lt;/p&gt;
&lt;?php endif ?&gt;</code></pre>
  <p class="note">An example of a simple article 3 to 8 paragraphs in length, with optionally appearing intro text and related story elements.</p>
</div>

</article>
</section>

<section>
<h2 id="queries">Loop positions and request queries</h2>
<p>Inside a loop, <code>dumb_question("order:first")</code>, <code>dumb_question("order:last")</code>, and <code>dumb_question("order:inner")</code> test the current position. <code>dumb_luck("")</code> prints the one-based item number.</p>
<p><code>dumb_question("url:mode=compact")</code> tests a query parameter, and <code>dumb_question("segment:1=news")</code> tests the first URL path segment. Laravel uses its current request automatically. See <a href="#configuration">paths, cache and image settings</a> for configuration.</p>
<p>In the meantime, <a href="https://github.com/kerns/dummy">follow the project</a> on GitHub. Your ideas and contributions toward improving the code are welcome.</p>
</section>

</main>

<footer>
  <p>Dummy for rapid prototyping and QA &mdash; <a href="https://github.com/kerns/dummy">GitHub</a> &middot; <a href="https://github.com/kerns/dummy/blob/master/CREDITS.md">Image credits</a></p>
</footer>

</div>
</div>
<script type="module" src="demo_files/demo.js"></script>
</body>
</html>
