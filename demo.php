<? require_once("dummy/dummy.php") ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>Dummy - A Quick and Dirty Demo</title>
  <meta name="description" content="Dummy is a toolkit for rapid prototyping and QA.">
  <meta name="author" content="David Kerns">
  <meta name="viewport" content="width=device-width">

  <!-- Dummy Demo CSS -->
  <link href="demo_files/demo.css" rel="stylesheet" type="text/css" />

  <!-- For syntax highlighting. -->
  <link href="demo_files/sh/shCore.css" rel="stylesheet" type="text/css" />
  <link href="demo_files/sh/shThemeMidnight.css" rel="stylesheet" type="text/css" />

</head>

<body>

<header>
  <h1>A Quick and Dirty Dummy Demo</h1>
</header>

<div id="main" role="main">

<div class="hero-block">
  <p>This document contains a few working examples to get you started using Dummy, together with some light documentation on how it works. It also makes a suitable test page. If you can get it to operate without error then you're ready to start using it in your own project.</p>
  <p>If you're unclear as to why you'd want to do this in the first place, then head over to <a href="https://github.com/kerns/dummy">the project page</a>, or check out the <b>README.md</b> file included with this download.</p>
</div>

<section>
<h2>Getting Started</h2>

<article>
<h3>Basic Requirements</h3>
<p>You'll need a development environment running Apache and PHP compiled with support for GD (this hopefully covers most of them). Such an environment is provided out-of-the-box by Mac OS X. Alternatively, there are many <a href="http://en.wikipedia.org/wiki/MAMP">MAMP</a> and <a href="http://en.wikipedia.org/wiki/WAMP">WAMP</a> binaries available to both Mac and Windows users respectively. These are easy to install and configure and a good way to get started if you're on unfamiliar ground.</p>
</article>

<article>
<h3>Setup</h3>
<ol>
  <li><p>Move the main <b>/dummy</b> folder to the root level of your web project, and make sure any <b>.html</b> documents in which you plan to use Dummy are renamed with a <b>.php</b> extension. Next, make sure that any document in which you want to use Dummy has the following code on the very first line.</p>
    <div class="snippet code-snippet">
      <pre class="brush: php">
        &lt;? require_once("dummy/dummy.php") ?&gt;
      </pre>
    </div>
  </li>
  <li><p>Open the file located at <b>/dummy/dummy.php</b> and make sure the first line in the configuration section points to the location of <b>/dummy</b> relative to the web root of your project. If you're using a virtual host the default value is probably fine, and you don't need to change anything.</p></li>
  <li><p>Finally, make the cache folder located at <b>/dummy/cache</b> writable by your web server. Describing how to do this is outside the scope of this document, but finding out how is only <a href="http://www.google.com/search?q=how+to+make+a+folder+writable">a web search away</a>. If you see an image immediately below this block of text, it's working. A new image should appear every time you reload the page.</p></li>
</ol>

<div class="snippet img-snippet">
<img src="<? dummy("image@16:9,550x") ?>" width="550" alt="A test image..." />
<p class="note">
<?php
$filename = 'dummy/cache';
if (file_exists($filename) && is_writable($filename) ) {
    echo "Success. <strong>$filename</strong> exists and seems to be writable.";
} elseif (file_exists($filename)) {
    echo "<strong>$filename</strong> exists but it isn't writable by your web server. Please make it writable.";
} else {
    echo "<strong>$filename</strong> does not seem to exist. Create it and make sure it is writable by your web server.";
}
?>
</p>
</div>
</article>
</section>


<section>
<h2 id="assets">Dummy Assets</h2>
<p>The ability to select, manipulate, and insert different assets into your front-end project is a key feature of Dummy. All of the assets available to Dummy live in a folder located at <b>dummy/assets/</b>. It's a good idea to poke around in here and see how things are structured. The simple syntax used to request different assets relates very much to the structure of this directory.</p>

<article>
<h3>Dummy Text</h3>
<p>Dummy makes it easy to insert random strings of Lorem Ipsum that correspond to commonly needed lengths and formats. For example, the following snippet of code will produce a random string of Lorem Ipsum approximately the length of a headline.</p>

<div class="snippet code-snippet">
  <pre class="brush: php">
    &lt;? dummy("text@headline") ?&gt;
  </pre>
</div>

<p>The exact result will be different every time you reload your document, but it should look something like this...</p>

<div class="snippet text-snippet">
  <h4><? dummy("text@headline") ?></h4>
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
        <td><code>&lt;? dummy("text@headline") ?&gt;</code></td>
        <td><? dummy("text@headline") ?></td>
      </tr>
        <tr>
          <td>Teaser</td>
          <td><code>&lt;? dummy("text@teaser") ?&gt;</code></td>
          <td><? dummy("text@teaser") ?></td>
        </tr>
        <tr>
          <td>Short Teaser</td>
          <td><code>&lt;? dummy("text@short-teaser") ?&gt;</code></td>
          <td><? dummy("text@short-teaser") ?></td>
        </tr>
        <tr>
          <td>Long Teaser</td>
          <td><code>&lt;? dummy("text@long-teaser") ?&gt;</code></td>
          <td><? dummy("text@long-teaser") ?></td>
        </tr>
        <tr>
          <td>Item</td>
          <td><code>&lt;? dummy("text@item") ?&gt;</code></td>
          <td><? dummy("text@item") ?></td>
        </tr>
        <tr>
          <td>Date</td>
          <td><code>&lt;? dummy("text@date") ?&gt;</code></td>
          <td><? dummy("text@date") ?></td>
        </tr>
        <tr>
          <td>Time</td>
          <td><code>&lt;? dummy("text@time") ?&gt;</code></td>
          <td><? dummy("text@time") ?></td>
        </tr>
        <tr>
          <td>Time ago (relative)</td>
          <td><code>&lt;? dummy("text@time-ago") ?&gt;</code></td>
          <td><? dummy("text@time-ago") ?></td>
        </tr>
        <tr>
          <td>Author</td>
          <td><code>&lt;? dummy("text@author") ?&gt;</code></td>
          <td><? dummy("text@author") ?></td>
        </tr>
        <tr>
          <td>Username</td>
          <td><code>&lt;? dummy("text@username") ?&gt;</code></td>
          <td><? dummy("text@username") ?></td>
        </tr>
        <tr>
          <td>City</td>
          <td><code>&lt;? dummy("text@city") ?&gt;</code></td>
          <td><? dummy("text@city") ?></td>
        </tr>
        <tr>
          <td>Paragraph</td>
          <td><code>&lt;? dummy("text@paragraph") ?&gt;</code></td>
          <td><? dummy("text@paragraph") ?></td>
        </tr>
    </tbody>
</table>
<p class="note"><strong>Hint:</strong> Inside of <strong>dummy/assets/text/</strong> you'll find a directory full of flat text files. These are the basis for organizing different text formats used by Dummy. You can edit these or add your own as you like.</p>
</div>
</article>

<article>
<h3>Dummy Images</h3>
<p>Dummy makes it easy to crop and size placeholder images on the fly. It ships with a starter pack of high-quality, newsworthy, Creative Commons licensed images, but like other assets it's easy to edit or expand these according to the needs of your project.</p>
<p>The syntax for requesting an image is flexible. In a very basic example, you request an image with specific dimensions...</p>

<div class="snippet code-snippet">
  <pre class="brush: php">
    &lt;img src="&lt;? dummy("image@480x320") ?&gt;" /&gt;
  </pre>
</div>

<p>...and the following is returned:</p>

<div class="snippet img-snippet">
  <img src="<? dummy("image@480x320") ?>" />
</div>

<p>You can specify exact pixel dimensions or just an aspect ratio. You can also combine these parameters. For example, you can choose to specify just the width, together with an aspect ratio. Dummy will return the path to an image that conforms to both requirements.</p>

<div class="snippet code-snippet">
  <pre class="brush: php">
    &lt;img src="&lt;? dummy("image@640x,16:9") ?&gt;" /&gt;
  </pre>
</div>

<p>Returns...</p>

<div class="snippet img-snippet">
  <img src="<? dummy("image@640x,16:9") ?>" />
  <p class="note"><b>Note:</b> Dummy returns a URL path to an image, not the HTML needed to embed it.</p>
</div>

<p>The following table provides some clues as to how you can structure your image requests.</p>

<div class="snippet table-snippet">
<table summary="Dummy Image Assets" class="image-table">
    <thead>
      <tr>
        <th scope="col" class="objective">Objective</th>
        <th scope="col">Dummy Code</th>
        <th scope="col" class="output">Output</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>100px square <span>Classic. Square.</span></td>
        <td><code>&lt;? dummy("image@100x100") ?&gt;</code></td>
        <td><span><a href="<? dummy("image@100x100") ?>"><? dummy("image@100x100") ?></a></span></td>
      </tr>
      <tr>
        <td>100px square (Part II) <span>A rather pointless way of generating the same result as above. Proving a point here.</span></td>
        <td><code>&lt;? dummy("image@100x,1:1") ?&gt;</code></td>
        <td><span><a href="<? dummy("image@100x,1:1") ?>"><? dummy("image@100x,1:1") ?></a></span></td>
      </tr>
      <tr>
        <td>16:9 <span>With a height of 200px. Pixel dimensions and aspect ratios must be separated with a comma.</span></td>
        <td><code>&lt;? dummy("image@16:9,x200") ?&gt;</code></td>
        <td><span><a href="<? dummy("image@16:9,x200") ?>"><? dummy("image@16:9,x200") ?></a></span></td>
      </tr>
      <tr>
        <td>16:9 (Part II) <span>Same as above. It doesn't matter which order you specify ratio or pixel dimensions.</span></td>
        <td><code>&lt;? dummy("image@x200,16:9") ?&gt;</code></td>
        <td><span><a href="<? dummy("image@x200,16:9") ?>"><? dummy("image@x200,16:9") ?></a></span></td>
      </tr>
      <tr>
        <td>3:2 <span>The Photographer's aspect! We'll ask for assets from a specific subdirectory of &lsquo;dummy/assets/images/&rsquo; here to improve the odds of getting a suitable image.</span></td>
        <td><code>&lt;? dummy("image/landscape@3:2") ?&gt;</code></td>
        <td><span><a href="<? dummy("image@3:2") ?>"><? dummy("image@3:2") ?></a></span></td>
      </tr>
      <tr>
        <td>!Avatar <span>Subdirectories of &lsquo;dummy/assets/images/&rsquo; that begin with a &lsquo;!&rsquo; are exempted from normal random (and recursive) selection. You have to target them specifically. This is good.</span></td>
        <td><code>&lt;? dummy("image/!avatar@150x150") ?&gt;</code></td>
        <td><span><a href="<? dummy("image/!avatar@150x150") ?>"><? dummy("image/!avatar@150x150") ?></a></span></td>
      </tr>
    </tbody>
</table>
<p class="note"><strong>Note:</strong> When specifying dimensions, either as an exact pixel measurement or as an aspect ratio, describe the width first and height second (<b>Width</b>&nbsp;x&nbsp;<b>Height</b>).</p>
</div>

<p>The basic model for how Dummy selects assets is random and recursive. It begins looking for assets at the level you specify and drills down, only ignoring folders that start with a &lsquo;<b>!</b>&rsquo;. Dummy also works in the background to track which image was most recently served, to minimize the odds of inserting the same image twice in succession or in close proximity.</p>
</article>

<article>
<h3>Dummy Ads</h3>
<p>Inside of <b>/dummy/assets/ads</b> you'll find a host of folders representing many of the popular formats <a href="http://www.iab.net/guidelines/508676/508767/displayguidelines">as defined by the IAB</a>. The dummy code used to request and insert an ad follows the now familiar model.</p>

<div class="snippet code-snippet">
  <pre class="brush: php">
    &lt;? dummy("ad@120x90") ?&gt;
  </pre>
</div>

<p>Returns...</p>

<div class="snippet img-snippet">
  <? dummy("ad@120x90") ?>
</div>

<p>Note that a request for an ad returns a pre-formatted block of embed code containing an ad, as opposed to just the URL path to the ad (as is done with images). The reason for this is that we often need blocks of repetitious, ancillary markup to surround ad placements. This is definitely the case with regard to advertising in the Adobe&reg; Flash&reg; format.</p>
<p>The default embed code for both standards compliant image based ads and ads in the Flash format can be found on the root level of <b>/dummy/assets/ads</b> in the form of two files &mdash; <b>image.embed.php</b> and <b>flash.embed.php</b> respectively. You can override the use of these defaults by placing an edited copy of one or both of these files in the folder of the format you wish to override.</p>
<p>Note that the folders containing different formats also specify their dimensions (WxH). Dummy parses these folder names and interprets them as variables, which in turn can be used to set the width and height values of ads as they are parsed into the embed code. If you don't feel it necessary to pass these values to your embed code, you're free to add new formats in folders and name them however you like &mdash; just remember to create a custom embed for these formats.</p>
<p class="note"><b>Note:</b> For a lot of reasons, you may find it useful to enable or disable the insertion of Flash format ads under different testing scenarios. There is a global preference toggle in the top of <b>dummy.php</b> for controlling this. The default is disabled.</p>
</article>
</section>

<section>
<h2 id="luck">Dumb Luck</h2>
<p>The ability to insert randomly selected assets into a layout is great, but it's Dummy's simple logic for controlling probability and creating loop ranges that make it possible to quic flesh out highly variable, asset rich layouts. This is made possible by a function within Dummy named <b>dumb_luck</b>.</p>

<article>
<h3>Controlling Probability</h3>
<p>A basic building block of Dummy driven layouts, we can use <b>dumb_luck</b> to influence the probability that something will happen when the document is rendered. This helps answer the age old question, <em>&ldquo;What does this layout look like with or without <b>X</b> ?&rdquo;</em>, where X is an image, a special announcement, or a block of JavaScript that triggers some other chain of events.</p>

<div class="snippet code-snippet">
  <pre class="brush: php">
    &lt;? if (dumb_luck("50%")): ?&gt;
      There is a 50/50 chance of this happening.
    &lt;? endif ?&gt;
  </pre>
</div>

<p>Build out fallback or alternative outcomes using standard PHP if/else statements.</p>

<div class="snippet code-snippet">
  <pre class="brush: php">
    &lt;? if (dumb_luck("1%")): ?&gt;
      There is a very slim chance that this will happen...
    &lt;? else: ?&gt;
      This seems 99% more likely.
    &lt;? endif ?&gt;
  </pre>
</div>

<p>You can also nest <b>dumb_luck</b> logic as needed, to test and explore more complex scenarios or outcomes.</p>

<div class="snippet code-snippet">
  <pre class="brush: php">
    &lt;? if (dumb_luck("50%")): ?&gt;
      &lt;? if (dumb_luck("50%")): ?&gt;
        Heads
      &lt;? else: ?&gt;
        Tails
      &lt;? endif ?&gt;
    &lt;? else: ?&gt;
      &lt;? if (dumb_luck("33%")): ?&gt;
        Rock
      &lt;? elseif (dumb_luck("33%")): ?&gt;
        Paper
      &lt;? else: ?&gt;
        Scissors
      &lt;? endif ?&gt;
    &lt;? endif ?&gt;
  </pre>
  <p class="note">For the record... <b><? if (dumb_luck("50%")): ?><? if (dumb_luck("50%")): ?>Heads<? else: ?>Tails<? endif ?><? else: ?><? if (dumb_luck("50%")): ?>Rock<? elseif (dumb_luck("50%")): ?>Paper<? else: ?>Scissors<? endif ?><? endif ?></b>!</p>
</div>
</article>

<article>
<h3>Creating Loop Ranges</h3>

<div class="snippet img-snippet zoom-snippet">
	<? while (dumb_luck("50-100")): ?>
	<a href="#"><img src="<? dummy("image@80x80,")?>" width="40" height="40" alt="A thumbnail..." /></a>
  <? endwhile ?>
	<p class="note">Dumb Luck's loop range does more or less what it suggests. It takes whatever you place inside of it and loops it within a range of numbers that you specify.</p>
</div>

<p>In the example above, there are two bits of Dummy Code working together. <b>1)</b> A <b>dumb_luck</b> loop range of 50 to 100. <b>2)</b> Inside of that loop range, a single request for a 40x40 thumbnail. Together, they look like this:</p>

<div class="snippet code-snippet">
  <pre class="brush: php">
  &lt;? while (dumb_luck("50-100")): ?&gt;
    &lt;? dummy("image@40x40") ?&gt;
  &lt;? endwhile ?&gt;
  </pre>
</div>

<p>Dumb Luck's loop range makes it possible to generate massive amounts of variable content very quickly. We do so using the same <b>dumb_luck</b> function we use to control probability &mdash; only instead of passing in a % value, we'll pass it a range of two numbers separated with a "-". The PHP syntax also changes from &ldquo;IF&rdquo; to &ldquo;WHILE&rdquo;. In the example below, we ask for between 5 and 10 instances of a list item containing a headline.</p>

<div class="snippet code-snippet">
  <pre class="brush: php">
  <ul>
  &lt;? while (dumb_luck("5-10")): ?&gt;
    <li>&lt;? dummy("text@headline") ?&gt;</li>
  &lt;? endwhile ?&gt;
  </ul>
  </pre>
</div>

<p>The result is a simple UL list of between 5 and 10 list items:</p>

<div class="snippet text-snippet">
  <ul>
  <? while (dumb_luck("5-10")): ?>
    <li><? dummy("text@headline") ?></li>
  <? endwhile ?>
  </ul>
</div>
</article>

<article>
<h3>Putting It All Together</h3>

<p>Taking the same list used in the previous example, imagine we needed to see how it would look with <em>some</em> of the items linked <em>some</em> of the time. To achieve this we'll nest some probability logic inside of our loop range.</p>

<div class="snippet code-snippet">
  <pre class="brush: php">
    <ul>
    &lt;? while (dumb_luck("5-10")): ?&gt;
      &lt;? if (dumb_luck("50%")): ?&gt;
        <li><a href="#">&lt;? dummy("text@headline") ?&gt;</a></li>
      &lt;? else: ?&gt;
        <li>&lt;? dummy("text@headline") ?&gt;</li>
      &lt;? endif ?&gt;
    &lt;? endwhile ?&gt;
    </ul>
  </pre>
</div>

<p>Though the exact number will change every time you reload the page, roughly half of the items in the following list will be wrapped in a link. It's a very basic example, but one that uses all the core features of Dummy.</p>


<div class="snippet text-snippet">
  <ul><? while (dumb_luck("5-10")): ?><? if (dumb_luck("50%")): ?><li><a href="#"><? dummy("text@headline") ?></a></li><? else: ?><li><? dummy("text@headline") ?></li><? endif ?><? endwhile ?></ul>
</div>

<p>When used together, asset generation, probability, and loop range logic can create highly variable layouts with very little effort in code. This makes it possible to quickly test how the change of an item, attribute or parameter in one place propagates on a range of different scales. It can provide new insights on the performance or design efficacy of your work at the earliest stages of a project, and every time you reload the page.</p>


<div class="snippet code-snippet">
  <pre class="brush: php">
    <h1>&lt;? dummy("text@headline") ?&gt;</h1>
    &lt;? if (dumb_luck("25%")): ?&gt;
      <p class="sub-title">&lt;? dummy("text@long-teaser") ?&gt;</p>
    &lt;? endif ?&gt;
    <p>By &lt;? dummy("text@author") ?&gt; | Published &lt;? dummy("text@date") ?&gt;</p>
    &lt;? while (dumb_luck("3-8")): ?&gt;
      <p>&lt;? dummy("text@paragraph") ?&gt;</p>
    &lt;? endwhile ?&gt;
    &lt;? if (dumb_luck("75%")): ?&gt;
      <p><b>Related Story:</b> <a href="#">&lt;? dummy("text@headline") ?&gt;</a></p>
    &lt;? endif ?&gt;
  </pre>
  <p class="note">An example of a simple article 3 to 8 paragraphs in length, with optionally appearing sub-headline and related story elements.</p>
</div>

</article>
</section>

<section>
<h2>More to come...</h2>
<p>Some of Dummy's unfinished features will remain undocumented for now, namely the <b>dumb_question</b> function for reading and reacting to loop position, URL variables, and URL segments. More info to come on those when they're done.</p>
</section>

</div>

<footer>
  <p class="note">This page documents Dummy v. 1.0 &mdash; Ideas, questions, comments? <a href="http://twitter.com/kerns">Contact me</a>.</p>
</footer>

<script src="demo_files/sh/shCore.js" type="text/javascript"></script>
<script src="demo_files/sh/shBrushPHP.js" type="text/javascript"></script>

<script type="text/javascript">
     SyntaxHighlighter.all()
</script>


</body>
</html>