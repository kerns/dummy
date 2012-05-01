# Dummy
=====
Dummy is a simple PHP based toolkit, designed to make the development, testing and presentation of web prototypes less time consuming and more realistic.

Originally conceived as a tool for helping to simulate the high volume, rapidly changing content characteristics of online newspapers and magazines, it's generalized enough to benefit the early stage front-end development of any website or application with content that frequently changes in length, form or nature.

Dummy can be useful in the early stages of a project's development, where basic questions and assumptions about how to architect the front-end must be tested and resolved to conclusion before engineers further down the gantt diagram can begin the work of disassociating and obfuscating yours.

It can also been seen as a support tool for designers and front-end artisans ready to embrace the dark art of In-Browser Design.

### Dummy can…
+ Insert randomly selected strings of placeholder text in various lengths and formats (e.g. headline, teaser, paragraph).
+ Automagically crop and size high quality, news worthy, Creative Commons licensed placeholder images from a default pool – or from a specific folder or subset of folders that you provide.
+ Flesh out highly variable, asset rich layouts quickly with friendly, human-readable logic for building loops and controlling probability.
+ Allow you to focus on the important things – namely, the design and the code – not on copying and pasting (static) Lorum Ipsum, or hunting down plausible placeholder images.
+ More. Not lots more, but a _bit_ more!

### You can…
+ See demos …coming soon.
+ Read docs …also coming soon.
+ Give feedback …[this instant](http://twitter.com/kerns).
+ Fork it on Github @ [https://github.com/kerns/dummy](https://github.com/kerns/dummy)

### More Info
Dummy's primary goal is to provide tools that speed front-end prototyping and QA. Whether you're meticulously crafting a fully responsive site with a myriad of CSS breakpoints, or just slapping together a half-baked idea – using Dummy should make it easier _at the earliest stages of development_ to visualize, test and present something much closer to what your actual front-end will become under a variety of different conditions.

It does so by helping you to populate your work with content and randomizing key aspects of it's layout, in a way, simulating connectivity to a live database.

**Dummy is for the front-end web worker who wonders…**

+ How does this layout hold up with and without an image, or with multiple images?
+ What is the maximum number of items that can appear in this list before it breaks the layout, or before it just stops looking good?
+ How does the columns balance as the content changes – and what's the longest word that can fit into this column without triggering unwanted overflow?
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
+ Improve demos, finish docs.
+ Optimize performance and memory usage.
+ Integrate a Lorum Ipsum generator, together with some intuitive syntax for specifying parameters of the text you want generated (length in chars or words, casing, etc)
+ Ability to call and define image crops from within CSS (i.e. ability to post image requests in the URL to dummy.php)
+ Develop an easy way to link our broadcast dumb_luck outcomes, so that one outcome could bubble-over to another.
+ Clean up the way errors and messages are formatted (i.e. abstract the HTML and style surrounding them)
+ It's a bad idea to ship this with a bandwidth hogging library of images in assets/images. The image assets that ship with Dummy are also highly geared toward usage in editorial design. What if we maintained separate asset packages for different types of projects (e.g. a commerce package with commercial product shots, a portfolio or gallery package with images that showcase art, architecture, or design).

### License
Dummy is released under a Creative Commons Attribution-Share Alike 3.0 United States license (http://creativecommons.org/licenses/by-sa/3.0/us/). Be a doll and let me know how you're using Dummy, or if you've used it to help build, test or demo anything interesting.