<? require_once("dummy/dummy.php") // Dummy now at the ready ;?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>Dummy - A Quick and Dirty Demo</title>
  <meta name="description" content="">
  <meta name="author" content="">
  <meta name="viewport" content="width=device-width">

  <!-- Dummy demo css -->
  <link href="demo_files/dummy.css" rel="stylesheet" type="text/css" />
  
  <!-- For syntax highlighting. Only works if you're connected to the internet -->  
  <link href="http://alexgorbatchev.com/pub/sh/current/styles/shCore.css" rel="stylesheet" type="text/css" />
  <link href="http://alexgorbatchev.com/pub/sh/current/styles/shThemeDefault.css" rel="stylesheet" type="text/css" />
  <link href="http://alexgorbatchev.com/pub/sh/current/styles/shThemeMidnight.css" rel="stylesheet" type="text/css" />

</head>
<body>

<header>
  <h1>Quick and Dirty Dummy Demo</h1>
</header>

<div id="main" role="main">

<div class="hero-block">
  <p>This document contains a few working examples to get you started using Dummy, together with some light documentation on how it works. It also makes a great test page. If you can get this page to operate without any errors, then you're ready to start using it in your own project.</p>
  
  <p>If you're unclear as to why you'd want to do this in the first place, then head over to <a href="https://github.com/kerns/dummy">the project page</a>, or check out the <b>README.md</b> file included with this download.</p>
</div>

<h2>Getting Started</h2>
<h3>Basic Requirements</h3>

<p>You need a development environment running Apache + PHP compiled with support for GD (this hopefully covers most of them).</p>

<h3>Installation</h3>

<p>Move the dummy folder to the root level of your web project, and make sure any <b>.html</b> documents in which you plan to use Dummy are renamed with a <b>.php</b> extension. Alternatively, you can enable the <b>.htaccess</b> file located at <b>dummy/docs/optional.htaccess</b>. It will allow HTML documents to be parsed as PHP, along with some other nice features.</p>

<p>Then, for every document in which you want to use dummy code, add the following line just above your opening doctype declaration.</p>

<div class="snippet code-snippet">
  <pre class="brush: php">
    &lt;? require_once("dummy/dummy.php") // Dummy now at the ready ;?&gt;
    &lt;!doctype html&gt;
  </pre>
</div>

<p>So far so good. The next step is to make sure that the cache folder located at <b>dummy/cache/</b> is writable by your web server. For performance reasons, it's here that Dummy will first look for pre-existing crop sizes before generating news ones. Describing how to make a folder writable by your local web server is outside the scope of this document, but finding out how is only a web search away.</p>
  
<p>If you see an image immediately below this block of text, it's working. A new image should appear every time you reload the page.</p>

<div class="snippet img-snippet">
<img src="<? dummy("image@16:9,550x");?>" width="550" alt="A test image..." />
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


<h2>Dummy Assets</h2>

<p>The ability to select, manipulate, and insert different assets into your front-end project is a key feature of Dummy. All of the assets available to Dummy live in a folder located at <b>dummy/assets/</b>. It's a good idea to poke around in here and see how things are structured. The simple syntax used to request different assets relates very much to the structure of this directory.</p>

<h3>Dummy Text</h3>

<p>Dummy makes it easy to insert random strings of Lorem Ipsum that correspond to commonly needed lengths and formats. For example, the following snippet of code will produce a random string of Lorem Ipsum approximately the length of a headline.</p>
  
<div class="snippet code-snippet">
  <pre class="brush: php">
    &lt;? dummy("text@headline") ;?&gt;
  </pre>
</div>

<p>The exact result will be different every time you reload your document, but it should look something like this...</p>

<div class="snippet text-snippet">
  <h4><? dummy("text@headline") ;?></h4>
</div>

<p>The following table is a catalog of the text assets that currently ship with Dummy, the snippet of code used to insert the asset, and a sample of expected output.</p>

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
        <td><code>&lt;? dummy("text@headline") ;?&gt;</code></td>
        <td><? dummy("text@headline") ;?></td>
      </tr>
        <tr>
          <td>Teaser</td>
          <td><code>&lt;? dummy("text@teaser") ;?&gt;</code></td>
          <td><? dummy("text@teaser") ;?></td>
        </tr>
        <tr>
          <td>Short Teaser</td>
          <td><code>&lt;? dummy("text@short-teaser") ;?&gt;</code></td>
          <td><? dummy("text@short-teaser") ;?></td>
        </tr>
        <tr>
          <td>Long Teaser</td>
          <td><code>&lt;? dummy("text@long-teaser") ;?&gt;</code></td>
          <td><? dummy("text@long-teaser") ;?></td>
        </tr>
        <tr>
          <td>Item</td>
          <td><code>&lt;? dummy("text@item") ;?&gt;</code></td>
          <td><? dummy("text@item") ;?></td>
        </tr>
        <tr>
          <td>Date</td>
          <td><code>&lt;? dummy("text@date") ;?&gt;</code></td>
          <td><? dummy("text@date") ;?></td>
        </tr>
        <tr>
          <td>Time</td>
          <td><code>&lt;? dummy("text@time") ;?&gt;</code></td>
          <td><? dummy("text@time") ;?></td>
        </tr>
        <tr>
          <td>Time ago (relative)</td>
          <td><code>&lt;? dummy("text@time-ago") ;?&gt;</code></td>
          <td><? dummy("text@time-ago") ;?></td>
        </tr>
        <tr>
          <td>Author</td>
          <td><code>&lt;? dummy("text@author") ;?&gt;</code></td>
          <td><? dummy("text@author") ;?></td>
        </tr>
        <tr>
          <td>Username</td>
          <td><code>&lt;? dummy("text@username") ;?&gt;</code></td>
          <td><? dummy("text@username") ;?></td>
        </tr>
        <tr>
          <td>City</td>
          <td><code>&lt;? dummy("text@city") ;?&gt;</code></td>
          <td><? dummy("text@city") ;?></td>
        </tr>
        <tr>
          <td>Paragraph</td>
          <td><code>&lt;? dummy("text@paragraph") ;?&gt;</code></td>
          <td><? dummy("text@paragraph") ;?></td>
        </tr>
    </tbody>
</table>
<p class="note"><strong>Hint:</strong> Inside of <strong>dummy/assets/text/</strong> you'll find a directory full of flat text files. These are the basis for organizing different text formats used by Dummy. You can edit these or add your own as you like.</p>
</div>

<h3>Dummy Images</h3>

<p>Dummy makes it easy to crop and size placeholder images on demand. It ships with a starter pack of high-quality Creative Commons licensed images, but (like other assets) it's easy to edit or expand these according to the needs of your project.</p>

<p>The syntax for requesting an image is simple and flexible. In a very basic example, you request an image with specific dimensions...</p>

<div class="snippet code-snippet">
  <pre class="brush: php">
    &lt;img src="&lt;? dummy("image@480x320");?&gt;" /&gt;
  </pre>
</div>

<p>...and the following is returned.</p>

<div class="snippet img-snippet">
  <img src="<? dummy("image@480x320");?>" />
</div>
  
  
<p>You can specify exact pixel dimensions or just an aspect ratio, or you can combine one or more of these properties. For example, you could choose to specify just the width, together with an aspect ratio. Dummy would then return the path to an image that conforms to the specified width and aspect ratio.</p>

<div class="snippet code-snippet">
  <pre class="brush: php">
    &lt;img src="&lt;? dummy("image@640x,16:9");?&gt;" /&gt;
  </pre>
</div>

<div class="snippet img-snippet">
  <img src="<? dummy("image@640x,16:9");?>" />
  <p class="note"><b>Note:</b> Dummy's image function gives you a URL to an image &mdash; not the HTML needed to embed it.</p>
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
        <td><code>&lt;? dummy("image@100x100") ;?&gt;</code></td>
        <td><span><a href="<? dummy("image@100x100") ;?>"><? dummy("image@100x100") ;?></a></span></td>
      </tr>
      <tr>
        <td>100px square (Part II) <span>A rather pointless way of generating the same result as above. Proving a point here.</span></td>
        <td><code>&lt;? dummy("image@100x,1:1") ;?&gt;</code></td>
        <td><span><a href="<? dummy("image@100x,1:1") ;?>"><? dummy("image@100x,1:1") ;?></a></span></td>
      </tr>
      <tr>
        <td>16:9 <span>With a height of 200px. Pixel dimensions and aspect ratios must be separated with a comma.</span></td>
        <td><code>&lt;? dummy("image@16:9,x200") ;?&gt;</code></td>
        <td><span><a href="<? dummy("image@16:9,x200") ;?>"><? dummy("image@16:9,x200") ;?></a></span></td>
      </tr>
      <tr>
        <td>16:9 (Part II) <span>Same as above. It doesn't matter which order you specify ratio or pixel dimensions.</span></td>
        <td><code>&lt;? dummy("image@x200,16:9") ;?&gt;</code></td>
        <td><span><a href="<? dummy("image@x200,16:9") ;?>"><? dummy("image@x200,16:9") ;?></a></span></td>
      </tr>
      <tr>
        <td>3:2 <span>The Photographer's aspect! We'll ask for assets from a specific subdirectory of &lsquo;assets/images&rsquo; here to improve the odds of getting a suitable image.</span></td>
        <td><code>&lt;? dummy("image/landscape@3:2") ;?&gt;</code></td>
        <td><span><a href="<? dummy("image@3:2") ;?>"><? dummy("image@3:2") ;?></a></span></td>
      </tr>
      <tr>
        <td>!Avatar <span>Subdirectories of &lsquo;assets/images&rsquo; that begin with a &lsquo;!&rsquo; are exempted from normal random (and recursive) selection. You have to target them specifically. This is good.</span></td>
        <td><code>&lt;? dummy("image/!avatar@150x150") ;?&gt;</code></td>
        <td><span><a href="<? dummy("image/!avatar@150x150") ;?>"><? dummy("image/!avatar@150x150") ;?></a></span></td>
      </tr>
    </tbody>
</table>
<p class="note"><strong>Note:</strong> When specifying dimensions, either as an exact pixel measurement or as an aspect ratio, describe the width first and height second (<b>Width</b>&nbsp;x&nbsp;<b>Height</b>).</p>
</div>

<p>The basic model for how Dummy selects assets is random and recursive. It begins looking for assets at the level you specify and drills down, only ignoring folders that start with a &lsquo;<b>!</b>&rsquo;. You specify a subset of assets by changing the scope of your request.</p>


<h3>Dummy Ads</h3>
<p>The issues and requirements around supporting the easy insertion of advertising were difficult to navigate, and couldn't be supported in exact same way that images in Dummy are generally supported.</p>

<p>Inside of <b>dummy/assets/ads</b> you'll find a host of folders representing popular formats <a href="http://www.iab.net/guidelines/508676/508767/displayguidelines">as defined by the IAB</a>. Not all of them, but like any other assets this is easy for you to expand or edit these according to your needs.</p>

<p>The dummy code used to request an ad follows the now already familiar model.</p>

<div class="snippet code-snippet">
  <pre class="brush: php">
    &lt;? dummy("ad@120x90") ;?&gt;
  </pre>
</div>

<p>Returns...</p>

<div class="snippet img-snippet">
  <? dummy("ad@120x90");?>
</div>

<p>Note that a request for an ad returns a pre-formatted block of embed code containing an ad, as opposed just a URL path to the ad (as is done with normal images). The reason for this is that we often need blocks of repetitious, ancillary markup to surround ad placements. This is definitely the case with regard to Adobe&reg; Flash&reg; based advertising.</p>

<p>The default embed code for standards compliant image based ads, and Adobe&reg; Flash&reg; based ads can be found on the root level of <b>assets/ads</b> in the form of two files &mdash; <b>image.embed.php</b> and <b>flash.embed.php</b> respectively. You can override the default embed code used by any ad format by placing an edited copy of one or both of these files in the folder of the format you wish to override.</p>

<p>Inside of <b>dummy/assets/ads/</b> you'll see a complete list of available formats displayed as folders which specify their dimensions (WxH). Dummy parses these names and interprets them as variables which are used to set the width and height values of ads as they are parsed into the embed code. If you don't feel it necessary to pass these values to your embed code, you can add new formats in folders and name them however you like &mdash; just remember to create a custom embed for these formats.</p>

<p class="note"><b>Tip:</b> For a lot of reasons, you may find it useful to enable or disable the use of Adobe&reg; Flash&reg; based ads under different testing scenarios. There is a preference toggle in the top of <b>dummy.php</b> for controlling this. The default is disabled.</p>


<h2 id="logic">Dumb Luck</h2>

<p>The ability to insert randomly selected assets into a layout is great, but it's the combination of asset insertion with simple logic for controlling probability and creating loop ranges that make it possible for Dummy to flesh out asset rich document layouts in record time.</p>

<h3>Controlling Probability</h3>
<p>A basic building block of Dummy driven layouts, use it to control the probability that something will happen when the document is rendered. This helps answer the age old question, &rdquo;What does this layout look like with or without <b>X</b> ?&rdquo; It could be the appearance of an image, a special announcement, or a block of JavaScript that triggers some other chain of events.</p>

<div class="snippet code-snippet">
  <pre class="brush: php">
    &lt;? if (dumb_luck("50%")): ?&gt;
      There is a 50/50 chance of this happening.
    &lt;? endif ;?&gt;
  </pre>
</div>

<p>Build out fallback or alternative outcomes using standard PHP if/else statements.</p>

<div class="snippet code-snippet">
  <pre class="brush: php">
    &lt;? if (dumb_luck("1%")): ?&gt;
      There is a very slim chance that this will happen...
    &lt;? else: ?&gt;
      This seems 99% more likely.
    &lt;? endif ;?&gt;
  </pre>
</div>


<p>You can also nest calls to <b>dumb_luck</b> as needed, to test and explore more complex scenarios or outcomes.</p>

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
    &lt;? endif ;?&gt;
  </pre>
</div>

<p class="note">For the record... 
<b>
<? if (dumb_luck("50%")): ?>
<? if (dumb_luck("50%")): ?>Heads<? else: ?>Tails<? endif ;?>
<? else: ?>
<? if (dumb_luck("50%")): ?>Rock<? elseif (dumb_luck("50%")): ?>Paper<? else: ?>Scissors<? endif ;?>
<? endif ;?>
</b> won.


<h3>Creating Loop Ranges</h3>

<div class="snippet img-snippet zoom-snippet">
	<? while (dumb_luck("50-90")): ?>
	<a href="#"><img src="<? dummy("image@200x200,");?>" width="40" height="40" alt="A thumbnail..." /></a>
	<? endwhile ;?>
</div>

<p>A basic building block of Dummy driven layouts, use it to control the probability that something will happen when the document is rendered. This helps answer the age old question, &rdquo;What does this layout look like with or without <b>X</b> ?&rdquo; It could be the appearance of an image, a special announcement, or a block of JavaScript that triggers some other chain of events.</p>

<div class="snippet text-snippet">
	<ul>
		<li><? dummy("text@headline") ;?></li>
	</ul>
</div>

</div>

<footer>
<p class="note">This page documents Dummy v. 1.0 &mdash; Ideas, questions, comments? <a href="http://twitter.com/kerns">Contact me</a>.</p>
</footer>


<script src="http://alexgorbatchev.com/pub/sh/current/scripts/shCore.js" type="text/javascript"></script>
<script src="http://alexgorbatchev.com/pub/sh/current/scripts/shAutoloader.js" type="text/javascript"></script>
<script src="http://agorbatchev.typepad.com/pub/sh/3_0_83/scripts/shBrushPHP.js" type="text/javascript"></script>


<script type="text/javascript">
     SyntaxHighlighter.all()
</script>

</body>
</html>