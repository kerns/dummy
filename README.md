# Dummy
=====
Dummy is a toolkit designed to make the development, testing and presentation of web prototypes less time consuming and more realistic. It does so by making it easy to populate your static HTML with dynamic content, and by randomizing elements within your layout, in a way, simulating connectivity to a live database.

Originally conceived as a tool for helping to simulate the high volume, rapidly changing content characteristics of online newspapers and magazines, it's generalized enough to benefit the early stage front-end development of any website or application with content that frequently changes in length, form or nature.

Dummy can be useful in the early stages of a project's development, where basic questions and assumptions about how to architect the front-end must be tested and resolved to conclusion before engineers further down the Gantt diagram can begin their work of disassociating and obfuscating yours.

It can also been seen as a support tool for designers and front-end artisans ready to embrace the dark art of designing in-browser, as vastly different layout permutations can be generated and evaluated rapidly and with a minimum of effort.

### Dummy can…
+ Insert randomly selected strings of placeholder text in various lengths and formats (e.g. headline, teaser, paragraph).
+ Automagically crop and size high quality, news worthy, Creative Commons licensed placeholder images from a default pool – or from a specific folder or subset of folders that you provide.
+ Flesh out highly variable, asset rich layouts quickly with friendly, human-readable logic for building loops and controlling probability.
+ Allow you to focus on the important things – namely, the design and the code – not on copying and pasting (static) Lorem Ipsum, or hunting down plausible placeholder images.
+ More. Not lots more, but a _bit_ more!

### You can…
+ See demos and read docs at http://dummy.kerns.dk/
+ Watch a screencast at https://vimeo.com/42252088
+ Ask questions and direct misgivings to http://twitter.com/kerns
+ Fork, follow or download via https://github.com/kerns/dummy

### More Info
Dummy's primary goal is to provide tools that speed front-end prototyping and QA. Whether you're meticulously crafting a fully responsive site with a myriad of CSS breakpoints, or just slapping together a half-baked idea – using Dummy should make it easier _at the earliest stages of development_ to visualize, test and present something much closer to what your actual front-end will become under a variety of different conditions.


**Dummy is for the front-end web worker who wonders…**

+ How does this layout hold up with and without an image, or with multiple images?
+ What is the maximum number of items that can appear in this list before it breaks the layout, or before it just stops looking good?
+ How do the columns align and balance as the amount of content changes – or what's the longest word that can fit into this column without triggering unwanted overflow?
+ What is the easiest way to demo the signed-in and signed-out states of this page?
+ Do these CSS breakpoints hold water?
+ How will this script perform in a DOM when it needs to process over 100+ images?

### Why?
Using Dummy in the early stages of front-end development can give you a new perspective on your work every time your document is reloaded in the browser, shortening and improving your test cycle by more quickly exposing weak-points or trouble areas as you go about marking up a new design.

This is especially important for front-end code that should be delivered to another team for implementation as part of a phased hand-off. As any front-ender knows, the earlier you can catch a problem the easier and less painful it will be to debug.

Dummy also aims to support designers and front-end artisans who find themselves increasingly attracted to In-Browser Design as a viable process for the creation, iteration, and client approval of their work. As does @beardedstudio. (http://blog.bearded.com/post/21447195970/mocking-up-is-hard-to-do)

### Requirements
You need a development environment running Apache + PHP and compiled with support for GD (this covers most of them). If you want to play with URL segments or other advanced techniques then you'll need to have mod_rewrite enabled, and you'll need to enable the .htaccess file (included in /dummy/extras/), but again, this probably covers most local development environments. If you're not developing locally, …why aren't you developing locally?

### TODO
+ Optimize performance and memory usage.
+ Integrate a Lorem Ipsum generator, together with some intuitive syntax for specifying parameters of the text you want generated (length in chars or words, casing, etc)
+ Ability to call and define image crops from within CSS (i.e. ability to post image requests in the URL to dummy.php)
+ Develop an easy way to link our broadcast dumb_luck outcomes, so that one outcome could bubble-over to another.
+ Clean up the way errors and messages are formatted (i.e. abstract the HTML and inline styles that surround them)
+ The image assets that ship with Dummy are highly geared for usage in editorial design. One could imagine separate asset packages for different types of projects (e.g. a commerce package with commercial product shots, a portfolio or gallery package with images that showcase art, architecture, or design).

### License
Dummy is released under a Creative Commons Attribution-Share Alike 3.0 United States license (http://creativecommons.org/licenses/by-sa/3.0/us/). Be a doll and let me know how you're using Dummy, or if you've used it to help build, test or demo anything interesting.

### Images
Credits and license info can be found in dummy/CREDITS.md
