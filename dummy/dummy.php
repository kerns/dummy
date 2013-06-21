<?php

// +-+-+-+-+-+ +-+-+-+-+-+-+-+ +-+-+-+
// Dummy v. 1.0
// For Rapid Prototyping and QA
// +-+-+-+-+-+ +-+-+-+-+-+-+-+ +-+-+-+

// Feedback, requests, ideas => http://twitter.com/kerns
// Fork, Follow, Download => https://github.com/kerns/dummy

// LICENSE
// Provided under a Creative Commons Attribution-Share Alike 3.0 United States license
// (http://creativecommons.org/licenses/by-sa/3.0/us/).

// REQUIREMENTS
// Apache + PHP + GD

// BASIC CONFIGURATION
$dummy_path = "/dummy"; // The location of "/dummy" relative to the webroot of your project. (Example: "/~username/Sites/ProjectName/dummy")
$flash_ads = false; // (true or false) Incorporate Adobe® Flash® based ads in the random selection and insertion of ads?
define("JPEG_QUALITY", 90); // (1-100) You understand...
define("MAX_WIDTH_PX", 2048); // (value in px) Max width of an image in assets/images
define("MAX_HEIGHT_PX", 1536); // (value in px) Max height of an image in assets/images

// ADVANCED CONFIGURATION
$delimiter = '@'; // Leave this, no reason to change it.
define("ADVANCED_RANDOM_IMAGES", true); // (true or false) Minimizes the number of repeated images by way of opening a session. This is good.
define("IGNORE_CACHE", false); // For debugging of image generation
define("HIDDEN_FOLDER_PREPEND", "!"); // Prepended to the name of a folder to be excluded from normal random selection (Example: "!myfolder")
ini_set("memory_limit", "256M"); // Boosts memory allocation
// END CONFIGURATION

// BEGIN DUMMY
// Start a session if it hasn't already been started
$sid = session_id();
if (!$sid) {
   session_start(); 
}

// Clear the list of used images
$_SESSION["USED_RANDOM_IMAGES"] = array();

// CORE DUMMY CODE (Deals with everything in dummy/assets)
function dummy_filter_strings($a) {
   $a = trim($a);
   if ($a == '') return false;     // Remove empty lines
   if ($a{0} == '#') return false; // Remove comments
   return true;
}

function dummy_text($inpath, $filename) {
   $path = dirname(__FILE__) .  "/assets/" . $inpath;

   $strings = array();
   if ($filename) {
      $strings = file("$path/$filename.txt"); // Load the content of this file
   } else {
      $basePath = dirname(__FILE__)."/assets/$inpath/";
      $files = getFilesRecursively($basePath);
      foreach ($files as $file) {
         $strings = array_merge($strings, file($basePath.$file));
      }
   }

   // Remove unwanted strings
   $strings = array_filter($strings, 'dummy_filter_strings');

   // Any strings at all?
   if (count($strings) == 0) {
      echo "<em>Couldn't locate anything usable in <b>&#8220;/assets/$inpath@$filename&#8221;</b>. Please double check your Dummy Code.</em>";
      return;
   }

   // Select random string among all the possible strings
   $idx = array_rand($strings);

   // Perform magic (do a php-parse of the string)
   ob_start();
   eval('?' . '>' . $strings[$idx]);
   $content = trim(ob_get_contents());
   ob_end_clean();

   // Print the resulting string
   echo $content;
}

function dummy_ad($folder) {
   global $flash_ads;
   $path = dirname(__FILE__) . "/assets/ad";

   // Find all possible ads
   if (!$folder) $folder = '*';
   if ($flash_ads) {
      $ads = glob("$path/$folder/*.{jpg,jpeg,gif,png,swf}", GLOB_BRACE);
   } else {
      $ads = glob("$path/$folder/*.{jpg,jpeg,gif,png}", GLOB_BRACE);
   }

   // Select a random ad
   $ad = $ads[array_rand($ads)];

   // Decide which embed to use
   $overridepath = dirname($ad);
   if (substr($ad, -4) == '.swf') {
      $inc = 'flash.embed.php';
   } else {
      $inc = 'image.embed.php';
   }
   // Doublecheck for overrides
   if (file_exists(dirname($ad) . "/$inc")) {
      $useinc = dirname($ad) . "/$inc"; // Use the override
   } else {
      $useinc = "$path/$inc";     // Use the default
   }

   // Which size did we choose?
   $paths = array_reverse(explode('/', $ad));
   $size = $paths[1];
   list($width,$height) = explode('x', $size);

   // What is the URL of the ad?
   global $dummy_path;
   $ad = "$dummy_path/assets/ad/$size/" . rawurlencode(basename($ad));

   // Perform magic
   include($useinc);

}

function dummy_image($path, $params, $pathtype = "URI") {
   global $dummy_path;
   $basePath = dirname(__FILE__)."/assets/image/";
   $imgFn = getRandomFile($basePath, $path);

   if ($params == "") {
      if ($pathtype == "URI") {
         $result = $dummy_path."/assets/image/".$imgFn;
      } else {
         $result = $basePath . $imgFn;
      }
   } else {
      $params = str_replace(" ", "", $params);
      $params = explode(",", $params);
      $imgFnPath = pathinfo($imgFn);
      $nImgExt = $imgFnPath["extension"];

      list($ox, $oy, $nImgtype, $nImgattr) = getimagesize($basePath.$imgFn);
      $ax = 0;
      $ay = 0;
      $rx = 0;
      $ry = 0;
      foreach($params as $param) {
         if (preg_match("/^(\d{0,5})x(\d{0,5})$/", $param, $m)) {
            $rx = (int)$m[1]; // Getting the width and making sure it's in the allowed range
            $ry = (int)$m[2]; // Getting the height and making sure it's in the allowed range
            if ($rx > MAX_WIDTH_PX) {
               $rx = 0;
            }
            if ($ry > MAX_HEIGHT_PX) {
               $ry = 0;
            }
         } else if (preg_match("/^([\d\.]+):([\d\.]+)$/", $param, $m)) {
            // Derive the aspect ratio
            $ax = $m[1];
            $ay = $m[2];
         }
      }
      $cx = $ox;
      $cy = $oy;

      if ($rx > 0 && $ry > 0) {
         // If both the width and height are specifed, we have our aspect ratio (width:height)
         $ax = $rx;
         $ay = $ry;
         $rx = 0;
      }

      if ($ax > 0 && $ay > 0) {
         $ar = $ax / $ay;
         if ($ar > 1) {
            // For landscape oriented images (width > height)
            if ($ox/$oy >= $ar) {
               $cy = $oy;
               $cx = $cy * $ar;
            } else {
               $cx = $ox;
               $cy = $cx / $ar;
            }
         } else if ($ar == 1) {
            // For squared images (width = height)
            $cx = min($ox, $oy);
            $cy = min($ox, $oy);
         } else {
            // For portrait oriented images (width < height)
            if ($ox/$oy >= $ar) {
               $cy = $oy;
               $cx = $cy * $ar;
            } else {
               $cx = $ox;
               $cy = $cx / $ar;
            }
         }
      }

      $ix = floor($cx);
      $iy = floor($cy);
      if ($rx > 0 && $ry > 0) {
      } else {
         if ($rx > 0 && $ry == 0) {
            // Calculate the new height
            $iy = floor($cy * $rx / $cx);
            $ix = $rx;
         } else if ($rx == 0 && $ry > 0) {
            // Calculate the new width
            $ix = floor($cx * $ry / $cy);
            $iy = $ry;
         }
      }


      // Image for output
      $nImgUrl = sprintf("%s/cache/%s_%sx%s.%s", $dummy_path, getCacheFilename($imgFnPath["filename"]), $ix, $iy, $nImgExt);

      // Image for saving file
      $nImgFn = sprintf("%s/cache/%s_%sx%s.%s", dirname(__FILE__), getCacheFilename($imgFnPath["filename"]), $ix, $iy, $nImgExt);

      // Saving image
      if (!file_exists($nImgFn) || IGNORE_CACHE) {
         $img = imagecreatefromstring(file_get_contents($basePath.$imgFn));
         $cImg = $img;
         if ($ax > 0 && $ay > 0) {
            // Create a cropped image
            $cImg = getTrueColorImg($img, $cx, $cy, $nImgExt);
            imagecopy($cImg, $img, 0, 0, ($ox-$cx)/2, ($oy-$cy)/2, $cx, $cy);
         }
         // Resize the image
         $nImg = getTrueColorImg($img, $ix, $iy, $nImgExt);
         imagecopyresampled($nImg, $cImg, 0, 0, 0, 0, $ix, $iy, $cx, $cy);

         if ($nImgExt == "jpg" || $nImgExt == "jpeg") {
            @imagejpeg($nImg, $nImgFn, JPEG_QUALITY);
         } else if ($nImgExt == "gif") {
            @imagegif($nImg, $nImgFn);
         } else if ($nImgExt == "png") {
            @imagepng($nImg, $nImgFn);
         }
      // Remove the images from memory
      imagedestroy($img);
      imagedestroy($cImg);
      }
      if ($pathtype == "URI") {
         $result = $nImgUrl;
      } else {
         $result = $nImgFn;
      }
   }
   print $result;
}

function getTrueColorImg($img, $ix, $iy, $ext) {
   $rImg = imagecreatetruecolor($ix, $iy);
   if ($ext == "gif" || $ext == "png") {
      $trnprt_indx = imagecolortransparent($img);
      if ($trnprt_indx >= 0) {
         $trnprt_color = imagecolorsforindex($img, $trnprt_indx);
         $trnprt_indx = imagecolorallocate($rImg, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
         imagefill($rImg, 0, 0, $trnprt_indx);
         imagecolortransparent($rImg, $trnprt_indx);
      } elseif ($ext == "png") {
         imagealphablending($rImg, false);
         $color = imagecolorallocatealpha($rImg, 0, 0, 0, 127);
         imagefill($rImg, 0, 0, $color);
         imagesavealpha($rImg, true);
      }
   }
   return $rImg;
}

function getCacheFilename($fn) {

   // Replace bad characters with a "_"
   return preg_replace("/\W/", "_", $fn);
}

function getRandomFile($basePath, $path) {
   if (substr($path, 0, 1) == "/") {
      $path = substr($path, 1);
   }

   // Getting the list of images already used...
   $sfiles = $_SESSION["USED_RANDOM_IMAGES"];
   $files = getFilesRecursively($basePath, $path);
   if (ADVANCED_RANDOM_IMAGES) {

      // Ignore used images
      $ffiles = $files;
      foreach ($ffiles as $k=>$v) {
         if (array_key_exists($v, $sfiles))  {
            unset($ffiles[$k]);
         }
      }
      if (count($ffiles) > 0) {
         $files = $ffiles;
      } else {

         // All images have been used, so let's clear the list
         $sfiles = array();
      }
   }
   $res = null;
   if (count($files) > 0) {

      // Get a random image
      srand();
      $res = $files[array_rand($files)];
      $sfiles[$res] = 1;
   }

   // Update the list of used images
   $_SESSION["USED_RANDOM_IMAGES"] = $sfiles;
   return $res;
}

function getFilesRecursively($basePath, $path = "", $files = array()) {

   // Scan directories recursively
   $dn = $basePath.$path;
   if ($dh = opendir($dn)) {
      while (($de = readdir($dh)) !== false) {

         // Take all filenames/folders which not beginning with "."
         if (substr($de, 0, 1) != "." && substr($de, 0, 1) != HIDDEN_FOLDER_PREPEND) {
            if (is_dir($dn.$de)) {

               // Explore subdirectories
               $files = getFilesRecursively($basePath, $path.$de."/", $files);
            } else {
               $files[] = $path.$de;
            }
         }
      }
   }
   return $files;
}

function dummy($a, $internalcall = false) {
   global $delimiter;

   // Parse the command
   if (strpos($a, $delimiter) !== false) {
      list($cmdraw, $params) = explode($delimiter, $a, 2);
   } else {
      $cmdraw = $a;
      $params = "";
   }
   $cmd = strtolower($cmdraw);

   // Branch out to what type we need to handle
   if ($cmd == 'ad') {
      dummy_ad($params);
   } else if (strpos($cmd, "image") !== false && strpos($cmd, "image") == 0) {
      $path = str_replace("image", "", $cmd);
      if (substr($path, -1) != "/") {
         $path.= "/";
      }
      dummy_image($path, $params, $internalcall ? 'FILE' : 'URI');
   } else {
      dummy_text($cmdraw, $params);
   }

}

// Check for the presense of a direct call
// If that is the case, we call dummy() with the query part
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $qs = $_SERVER['QUERY_STRING'];
    if (getenv('DUMMYURL')) {
        $qs = preg_replace('/^dummyurl=&/','', $qs);
    }
    ob_start();
    dummy($qs, true);
    $content = ob_get_contents();
    ob_end_clean();
    // figure out the image type
    $imgtype = exif_imagetype($content);
    if (!$imgtype) {
        echo "Could not open image $content";
        exit;
    }
    $contenttype = image_type_to_mime_type($imgtype);
    header("Content-type: " . $contenttype);
    readfile($content);
    exit();

}


// SO ENDS THE DUMMY CORE


// If Dummy had an expansion pack, this would be it.
// DUMB LUCK (Assigns probability and generates loops)

function dumb_luck($p) {
   /* Since we need to support nested loops, we will use a stack to maintain
   * each nesting. On the stack we put frames with the state of each nested loop.
   * Each frame contains a reference to the dumb_luck()-call in the condition of
   * the while loop, together with the number of total iterations and the number
   * of used iteration.
   *
   * What is a stack? A standard datastructure. See
   * http://en.wikipedia.org/wiki/Stack_(data_structure)
   * for more details.
   */
   
   static $luckstack = array();

   // Handle normal randomness ("50%", "50")
   if (strpos($p, '%')/* || is_numeric($p)*/) {
      return rand(0,99) < intval($p);
   }

   // Have we been asked to print the index?
   if ($p == '') {
      if (count($luckstack) == 0) { echo "<em>Not inside <b>dumb_luck</b></em>"; return false; }
      $frame = $luckstack[0];
      echo $frame['pos']+1;
      return;
   }

   // Are we on the 'first' item of a random list?
   if ($p == 'first') {
      if (count($luckstack) == 0) { echo "<em>Not inside <b>dumb_luck</b></em>"; return false; }
      $frame = $luckstack[0];
      return $frame['pos'] == 0;
   }

   // Are we at the 'last' item of a random list?
   if ($p == 'last') {
      if (count($luckstack) == 0) { echo "<em>Not inside <b>dumb_luck</b></em>"; return false; }
      $frame = $luckstack[0];
      return $frame['pos'] == $frame['count']-1;
   }

   // Are we some place in the middle of a random list?
   if ($p == 'inner') {
      if (count($luckstack) == 0) { echo "<em>Not inside <b>dumb_luck</b></em>"; return false; }
      $frame = $luckstack[0];
      return $frame['pos'] != $frame['count']-1 && $frame['pos'] != 0;
   }

   // Are we looping on a range?
   if (strpos($p, '-') === false && !is_numeric($p)) {
      echo "<em>Wrong range given to <b>dumb_luck</b></em>";
      return false;
   }

   // Find out which call we are looping. We use the backtrac to find
   // some form of way of referring to the actual dumb_luck()-call we
   // are looping on.
   $stack = debug_backtrace();
   $ref = md5($stack[0]['file'] . $stack[0]['line'] . serialize( $stack[0]['args'] ));

   // Do we already have a frame for this call?
   if (count($luckstack) > 0) {
      if ($luckstack[0]['ref'] == $ref) {
         // Continue with the old frame
         if (++$luckstack[0]['pos'] < $luckstack[0]['count']) {
            return true; // Keep looping
         } else {
            array_shift($luckstack); // Done looping, remove frame
            return false;
         }
      }
   }

   // range or specific amount?
   if (strpos($p, '-') === false) {
      $count = intval($p); // doesn't work at the moment. Conflicts with "x%" (see first case in this function)
   } else {
      // Create new frame
      $range = explode('-', $p,2);
      // How many loops?
      $count = rand(min($range[0], $range[1]), max($range[0],$range[1]));
   }

   $frame = array(
        'count' => $count,
        'pos' => 0,
        'ref' => $ref,
   );

   // Enqueue frame on our stack, whatever that means...
   if ($count > 0) {
      array_unshift($luckstack, $frame);
      return true;
   } else {
      return false;
   }

}

// DUMB QUESTION (Gets answers to important questions)

function dumb_question($p) {
   // Parse the command
   list($cmd,$params) = explode(':',$p,2);

   // If the command is 'order', delegate to dumb_luck
   if ($cmd == 'order') {
      return dumb_luck($params);
   }

   // If the command is 'url', we are going to check the query string
   if ($cmd == 'url') {
      // Parse the params into key/value pairs
      $params = str_replace(" ", "", $params);
      if (!$params) {
         if (count($_GET) > 0) {
            $pre = '?';
         } else {
            $pre = '';
         }

         return $pre . http_build_query($_GET);
      }
      $pairs = explode('&',$params);
      foreach ($pairs as $pair) {
         // Parse the pair into a key and a value
         list($key,$value) = explode('=',$pair,2);
         if ($value === null) {
            // just check for this in the url:
            if (!array_key_exists($key, $_GET)) return false;
         } else {
            // Check if the given question is not true
            if (!array_key_exists($key, $_GET)) {
                    return false;
            } else if ($_GET[$key] != $value) {
               return false;
            }
         }
      }
      // All the questions were "not not false", so they must all be true. It's a crazy world.
      return true;
   }

   // For 'segment' commands, we check the .htaccess generated query string   
   if ($cmd == 'segment') {
      // Requirement check
      if (!getenv('DUMMYURL')) {
         echo "<em>You can't ask about <b>segments</b> using dumb_question without first enabling the optional .htaccess file. Look in &#8220;dummy/docs&#8221; for more info.</em>";
         return false;
      }
      $segments = explode('/',$_GET['dummyurl']); // note that $segments[0] = '' after this
      if (!$params || strpos($params, '=') === false) {
         // Print the segment content
         if (!$params) {
            return $_GET['dummyurl'];
         }
         $n = intval($params);
         if ($n >= count($segments)) {
            return;
         }
         echo $segments[$n];
         return;
      } else {
         // Test the segment content
         // This is the same approach as in the above code for command 'url'
         $params = str_replace(" ", "", $params);
         $pairs = explode('&', $params);
         foreach ($pairs as $pair) {
            list($key,$value) = explode('=',$pair,2);
            if ($key >= count($segments)) return false; // make sure we don't index too far
            if ($segments[$key] != $value) return false;
         }
         return true;
      }
   }

   // Wrong command, you will be punished!
   echo "<em>Unknown command in <b>dumb_question:</b> $cmd</em>";
   return false;
}

/* eof */
