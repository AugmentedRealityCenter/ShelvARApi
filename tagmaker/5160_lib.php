<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

/**
 * @file
 * @author Bo Brinkman
 * @date 2011-11-20
 *
 * Generate a PNG image that prints as a sheet of 5160 labels.
 * 
 * @param $tag1 First tag, in base 64 format
 * @param $tag2 ... $tag30 Remaining tags, in base 64 format
 */

include 'tagmaker_lib.php';

function make_5160($taglist){
/* 5160 info:
 * Size: 2.625" x 1"
 * Labels per sheet: 30
 * Margins: Top 0.5", Bottom 0.5", Left 0.21975", Right 0.21975"
 */
$num_tags = 30;
$dpi = 300;
$width_5160 = 11*$dpi;
$height_5160 = 8.5*$dpi;

$tagwidth = 1.0*$dpi;
$tagheight = 2.625*$dpi;
$tagpadding = 0.1*$dpi;

$column_separation = 0.09275*$dpi;

$top_margin = 0.5*$dpi;
$bottom_margin = 0.5*$dpi;
$left_margin = 0.21975*$dpi;
$right_margin = 0.21975*$dpi;

//We are printing landscape!
$cur_x = $top_margin;
$cur_y = $right_margin;

$image_5160 = imagecreatetruecolor($width_5160,$height_5160);
$bgcolor = imagecolorallocate($image_5160,255,255,255);
$bordercolor = imagecolorallocate($image_5160,192,192,192);
imagefill($image_5160,0,0,$bgcolor);


for($i = 0; $i < $num_tags; $i++){
  if(array_key_exists('tag' . $i,$taglist)){
    $tag = $taglist['tag' . $i];
    $image2 = tagGenPNG($tag,0);
    $width2 = imagesx($image2);
    $height2 = imagesy($image2);

    $scalefactor = ($tagwidth-2*$tagpadding) / $width2;
    if(($tagheight-2*$tagpadding)/$height2 < $scalefactor){
      $scalefactor=($tagheight-2*$tagpadding) / $height2;
    }
    $tw = $width2*$scalefactor;
    $th = $height2*$scalefactor;
    imagerectangle($image_5160,$cur_x,$cur_y,$cur_x+$tagwidth,$cur_y+$tagheight,$bordercolor);

    $yoffset = $tagheight - $th - 3*$tagpadding;
    $xoffset = $tagwidth - $tw - 2*$tagpadding;

    imagecopyresampled($image_5160,$image2,$cur_x+$tagpadding+$xoffset,$cur_y+$tagpadding+$yoffset,0,0,$tw,$th,$width2,$height2);
    imagedestroy($image2);
  }
  $cur_x = $cur_x + $tagwidth;
  if($cur_x >= $width_5160 - $bottom_margin){
    $cur_y = $cur_y + $tagheight + $column_separation;
    $cur_x = $top_margin;
  }
}

$image_logo = imagecreatefrompng('../ShelvARLogo_Big_Left.png');
$logo_w = imagesx($image_logo);
$logo_h = imagesy($image_logo);
$logo_scale = $logo_w / (0.4*$bottom_margin);
$logo_ws = $logo_w/$logo_scale;
$logo_hs = $logo_h/$logo_scale;
imagecopyresampled($image_5160,$image_logo,0.5*$bottom_margin,2.5*$dpi-$logo_hs,0,0,$logo_ws,$logo_hs,$logo_w,$logo_h);
imagecopyresampled($image_5160,$image_logo,$width_5160-0.9*$bottom_margin,2.5*$dpi-$logo_hs,0,0,$logo_ws,$logo_hs,$logo_w,$logo_h);

//Name picked to avoid collisions between users
$file_name = time() . mt_rand(1000000,9999999) . '.png';

imagePNG($image_5160,'imgcache/' . $file_name);
imagedestroy($image_5160);
//Got this by hexediting a file that had been modified to be the correct DPI.
// Basically, this revolves around the ASCII string pHYs in the file. Get the 4
// bytes BEFORE pHYs (which is the size of DATA of the chunk, in bytes), and then everything
// up to the 4 bytes before the next chunk (which will be indicated by another 4 character
// human-readable string). The chunk has 4 bytes of length, 4 bytes of tag, n bytes of data,
// where n == the number in the first 4 bytes, and then 4 bytes of CRC. So this code is 
// 21 bytes long because it has 9 bytes of data and 12 bytes of length, tag, and CRC
$dpi_string = "\x00\x00\x00\x09\x70\x48\x59\x73\x00\x00\x2E\x23\x00\x00\x2E\x23\x01\x78\xA5\x3F\x76";
$file_contents = file_get_contents('imgcache/' . $file_name);
unlink('imgcache/' . $file_name);
//dpi_string needs to be inserted 4 bytes before the first IDAT
$loc = strpos($file_contents,'IDAT'); //Finds first occurence of IDAT
$loc = $loc - 4; //Place to insert

return substr($file_contents,0,$loc) . $dpi_string . substr($file_contents,$loc);
}
?>
